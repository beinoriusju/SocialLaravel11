<?php

namespace App\Livewire\Components;

use App\Models\EventCategory;
use App\Models\EventSubCategory;
use App\Models\Event;
use App\Models\EventMedia;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class CreateEvent extends Component
{
    use WithFileUploads;

    public $title;
    public $description;
    public $event_date;
    public $images = [];
    public $video;
    public $categories = [];
    public $subcategories = [];
    public $eventCategory;
    public $eventSubCategory;

    public function mount()
    {
        // Fetch all event categories with their subcategories
        $this->categories = EventCategory::with('eventSubCategories')->get();
    }

    // Update subcategories when a category is selected
    public function updatedEventCategory($categoryId)
    {
        if ($categoryId) {
            // Fetch subcategories related to the selected event category
            $this->subcategories = EventSubCategory::where('category_id', $categoryId)->get();

            // If no subcategories are found, reset the selected subcategory
            if ($this->subcategories->isEmpty()) {
                $this->eventSubCategory = null;
            }
        } else {
            // Clear subcategories and reset subcategory selection
            $this->subcategories = [];
            $this->eventSubCategory = null;
        }
    }

    public function createEvent()
    {
        // Validate the event inputs
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'eventCategory' => 'required|exists:event_categories,id',
            'eventSubCategory' => 'nullable|exists:event_subcategories,id',
            'images.*' => 'nullable|image|max:51200', // 50 MB in kilobytes
            'video' => 'nullable|mimes:mp4,avi,mkv|max:51200', // 50 MB in kilobytes
        ]);

        // Extract YouTube links from the title, description, and event details
        $youtubeLinks = $this->extractYouTubeLinks($this->title, $this->description);

        DB::beginTransaction();
        try {
            // Create the event
            $event = Event::create([
                'uuid' => Str::uuid(),
                'user_id' => auth()->id(),
                'title' => $this->title,
                'description' => $this->description,
                'event_date' => $this->event_date,
                'category_id' => $this->eventCategory,
                'subcategory_id' => $this->eventSubCategory,
            ]);

            // Save YouTube links (if any)
            if (!empty($youtubeLinks)) {
                foreach ($youtubeLinks as $link) {
                    EventMedia::create([
                        'event_id' => $event->id,
                        'file_type' => 'youtube', // Store YouTube links as 'youtube'
                        'file' => $link,
                    ]);
                }
            }

            // Process images and save to user-specific folder
            if ($this->images) {
                foreach ($this->images as $image) {
                    $imagePath = $image->store("events/{$event->user_id}/images", 'public');
                    EventMedia::create([
                        'event_id' => $event->id,
                        'file_type' => 'image',
                        'file' => $imagePath,
                    ]);
                }
            }

            // Process video and save to user-specific folder
            if ($this->video) {
                $videoFilePath = $this->video->store("events/{$event->user_id}/videos", 'public');
                EventMedia::create([
                    'event_id' => $event->id,
                    'file_type' => 'video',
                    'file' => $videoFilePath,
                ]);
            }

            DB::commit();

            // Emit an event to refresh the list of events
            $this->emit('reload');

            // Notify the user about the successful event creation
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Event created successfully!'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        // Clear the form fields
        $this->reset(['title', 'description', 'event_date', 'images', 'video', 'eventCategory', 'eventSubCategory']);
    }

    /**
     * Extract all YouTube video and playlist links from the title, description, and details.
     *
     * @param string|null $title
     * @param string|null $description
     * @return array
     */
    protected function extractYouTubeLinks($title, $description)
    {
        // Combine all fields into one string
        $content = $title . ' ' . $description;

        if (!$content) {
            return [];
        }

        // Regular expressions to match YouTube video and playlist URLs
        $videoPattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w\-]+)/i';
        $playlistPattern = '/(?:https?:\/\/)?(?:youtube\.com\/playlist\?list=)([\w\-]+)/i';

        $matches = [];
        preg_match_all($videoPattern, $content, $videoMatches);
        preg_match_all($playlistPattern, $content, $playlistMatches);

        // Combine video and playlist matches
        $matches = array_merge($videoMatches[0], $playlistMatches[0]);

        return $matches;
    }

    public function render()
    {
        return view('livewire.components.create-event');
    }
}
