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
    public $details; // Add this property for event details
    public $event_date;
    public $images = [];
    public $videos = []; // Multiple video support
    public $categories = [];
    public $subcategories = [];
    public $eventCategory;
    public $eventSubCategory;

    public function mount()
    {
        // Check if the user is an admin
        if (auth()->user()->role == 'admin') {
            // Fetch all categories for admins (including both admin and non-admin categories)
            $this->categories = EventCategory::where('status', 1)->get();
        } else {
            // Fetch only non-admin categories for regular users
            $this->categories = EventCategory::where('admin', 0)->where('status', 1)->get();
        }
    }

    // Update subcategories when a category is selected
    public function updatedEventCategory($categoryId)
    {
        if ($categoryId) {
            $this->subcategories = EventSubCategory::where('category_id', $categoryId)->get();

            // Reset subcategory if none found
            if ($this->subcategories->isEmpty()) {
                $this->eventSubCategory = null;
            }
        } else {
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
            'details' => 'nullable|string', // Add this for event details validation
            'event_date' => 'required|date',
            'eventCategory' => 'required|exists:event_categories,id',
            'eventSubCategory' => 'nullable|exists:event_subcategories,id',
            'images.*' => 'nullable|image|max:512000',
            'videos.*' => 'nullable|mimes:mp4,avi,mkv|max:512000',
        ]);

        // Extract YouTube links from the title, description, and details
        $youtubeLinks = $this->extractYouTubeLinks($this->title, $this->description, $this->details);

        DB::beginTransaction();
        try {
            // Create the event
            $event = Event::create([
                'uuid' => Str::uuid(),
                'user_id' => auth()->id(),
                'title' => $this->title,
                'description' => $this->description,
                'details' => $this->details, // Store the event details
                'event_date' => $this->event_date,
                'category_id' => $this->eventCategory,
                'subcategory_id' => $this->eventSubCategory,
            ]);

            // Save YouTube links
            if (!empty($youtubeLinks)) {
                foreach ($youtubeLinks as $link) {
                    EventMedia::create([
                        'event_id' => $event->id,
                        'file_type' => 'youtube',
                        'file' => $link,
                    ]);
                }
            }

            // Save images
            foreach ($this->images as $image) {
                $imagePath = $image->store("events/{$event->user_id}/images", 'public');
                EventMedia::create([
                    'event_id' => $event->id,
                    'file_type' => 'image',
                    'file' => $imagePath,
                ]);
            }

            // Save videos
            foreach ($this->videos as $video) {
                $videoPath = $video->store("events/{$event->user_id}/videos", 'public');
                EventMedia::create([
                    'event_id' => $event->id,
                    'file_type' => 'video',
                    'file' => $videoPath,
                ]);
            }

            DB::commit();

            // Emit event to refresh the list of events
            $this->dispatch('reload');

            // Notify the user
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Event created successfully!',
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        // Clear the form fields
        $this->resetForm();
    }

    /**
     * Extract YouTube links from the title, description, and details.
     *
     * @param string|null $title
     * @param string|null $description
     * @param string|null $details
     * @return array
     */
    protected function extractYouTubeLinks($title, $description, $details = null)
    {
        // Combine title, description, and details into one string for processing
        $content = $title . ' ' . $description . ' ' . $details;

        if (!$content) {
            return [];
        }

        // Regular expression pattern to match YouTube video URLs
        $videoPattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w\-]+)/i';

        preg_match_all($videoPattern, $content, $videoMatches);

        // Return unique YouTube video URLs
        return array_unique($videoMatches[0]);
    }

    public function resetForm()
    {
        $this->reset([
            'title', 'description', 'details', 'event_date', 'images', 'videos',
            'eventCategory', 'eventSubCategory',
        ]);
    }

    public function render()
    {
        return view('livewire.components.create-event');
    }
}
