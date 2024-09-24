<?php

namespace App\Livewire;

use App\Models\EventCategory;
use App\Models\EventSubCategory;
use App\Models\Event;
use App\Models\EventMedia;
use App\Models\EventAttendee;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Collection;

class Events extends Component
{
    use WithPagination, WithFileUploads;

    public $categories = [];
    public $subcategories = [];
    public $selectedCategory = null;
    public $selectedSubcategory = null;
    public $title;
    public $description;
    public $details;
    public $event_date;
    public $images = [];
    public $videos = []; // Support for multiple videos
    public $editMode = false;
    public $eventIdBeingEdited = null;
    public $attendees = [];
    public $startDate;
    public $endDate;

    // Property to hold events as a collection
    public $events;

    // Infinite scroll variables
    public $postsPerPage = 10;
    public $page = 1;
    public $hasMorePages = true;

    public function mount()
    {
        // Fetch all event categories with their subcategories
        $this->categories = EventCategory::with('eventSubCategories')->get();
        $this->events = collect(); // Initialize $events as a collection
        $this->loadMoreEvents();  // Load initial events
    }

    public function updatedSelectedCategory($categoryId)
    {
        if ($categoryId) {
            $this->subcategories = EventSubCategory::where('category_id', $categoryId)->get();
        } else {
            $this->subcategories = collect();
        }
        $this->selectedSubcategory = null;

        // Reset pagination and reload events when category changes
        $this->resetPagination();
        $this->loadMoreEvents(true);
    }

    public function updatedSelectedSubcategory()
    {
        // Reset pagination and reload events when subcategory changes
        $this->resetPagination();
        $this->loadMoreEvents(true);
    }

    public function updatedStartDate()
    {
        $this->resetPagination();
        $this->loadMoreEvents(true);
    }

    public function updatedEndDate()
    {
        $this->resetPagination();
        $this->loadMoreEvents(true);
    }

    public function createEvent()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'details' => 'nullable|string',
            'event_date' => 'required|date',
            'images.*' => 'nullable|image|max:51200',
            'videos.*' => 'nullable|mimes:mp4,mkv|max:51200', // Support multiple videos
        ]);

        $event = Event::create([
            'title' => $this->title,
            'description' => $this->description,
            'details' => $this->details,
            'event_date' => $this->event_date,
            'category_id' => $this->selectedCategory,
            'subcategory_id' => $this->selectedSubcategory,
            'user_id' => auth()->id(),
        ]);

        // Save media files
        $this->saveMedia($event);

        // Reset form after creating the event
        $this->resetForm();
    }

    public function editEvent($eventId)
    {
        $this->editMode = true;
        $this->eventIdBeingEdited = $eventId;

        $event = Event::findOrFail($eventId);

        $this->title = $event->title;
        $this->description = $event->description;
        $this->details = $event->details;
        $this->event_date = $event->event_date;
        $this->selectedCategory = $event->category_id;
        $this->selectedSubcategory = $event->subcategory_id;
    }

    public function updateEvent()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'details' => 'nullable|string',
            'event_date' => 'required|date',
            'images.*' => 'nullable|image|max:51200',
            'videos.*' => 'nullable|mimes:mp4,mkv|max:51200', // Support multiple videos
        ]);

        $event = Event::findOrFail($this->eventIdBeingEdited);

        $event->update([
            'title' => $this->title,
            'description' => $this->description,
            'details' => $this->details,
            'event_date' => $this->event_date,
            'category_id' => $this->selectedCategory,
            'subcategory_id' => $this->selectedSubcategory,
        ]);

        // Remove existing YouTube links and update new ones
        EventMedia::where('event_id', $event->id)
            ->where('file_type', 'youtube')
            ->delete();

        // Extract new YouTube links
        $youtubeLinks = $this->extractYouTubeLinks($this->title, $this->description);
        foreach ($youtubeLinks as $link) {
            EventMedia::create([
                'event_id' => $event->id,
                'file_type' => 'youtube',
                'file' => $link,
            ]);
        }

        // Save new media
        $this->saveMedia($event);

        // Reset form after updating the event
        $this->resetForm();
    }

    public function deleteEvent($eventId)
    {
        $event = Event::findOrFail($eventId);
        foreach ($event->media as $media) {
            \Storage::disk('public')->delete($media->file);
            $media->delete();
        }
        $event->delete();
    }

    private function saveMedia($event)
    {
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
    }

    public function resetForm()
    {
        $this->reset(['title', 'description', 'details', 'event_date', 'images', 'videos', 'selectedCategory', 'selectedSubcategory']);
        $this->editMode = false;
        $this->eventIdBeingEdited = null;
    }

    public function extractYouTubeLinks($title, $description)
    {
        $content = $title . ' ' . $description;
        if (!$content) return [];

        $videoPattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w\-]+)/i';
        preg_match_all($videoPattern, $content, $videoMatches);

        return array_unique($videoMatches[0]);
    }

    public function toggleAttendance($eventId)
    {
        $userId = auth()->id();
        $attendee = EventAttendee::where('event_id', $eventId)
            ->where('user_id', $userId)
            ->first();

        if ($attendee) {
            // User is already attending, so remove them
            $attendee->delete();
        } else {
            // User is not attending, so add them
            EventAttendee::create([
                            'event_id' => $eventId,
                            'user_id' => $userId,
                        ]);
                    }

                    // Reload events to reflect attendance change
                    $this->resetPagination();
                    $this->loadMoreEvents(true);
                }

                public function loadMoreEvents($reset = false)
                {
                    if ($reset) {
                        $this->events = collect();  // Initialize $events as a collection
                    }

                    $query = Event::with('media', 'attendees');

                    if ($this->selectedCategory) {
                        $query->where('category_id', $this->selectedCategory);
                    }

                    if ($this->selectedSubcategory) {
                        $query->where('subcategory_id', $this->selectedSubcategory);
                    }

                    if ($this->startDate) {
                        $query->whereDate('event_date', '>=', $this->startDate);
                    }

                    if ($this->endDate) {
                        $query->whereDate('event_date', '<=', $this->endDate);
                    }

                    $newEvents = $query->latest()
                        ->skip(($this->page - 1) * $this->postsPerPage)
                        ->take($this->postsPerPage)
                        ->get();

                    if ($newEvents->count() < $this->postsPerPage) {
                        $this->hasMorePages = false;
                    }

                    $this->events = $this->events->concat($newEvents); // Concatenate new events to the existing collection

                    $this->page++;
                }

                public function showAttendees($eventId)
                {
                    // Fetch the attendees for the selected event as a collection
                    $this->attendees = EventAttendee::where('event_id', $eventId)
                        ->with('user')
                        ->get();  // This will return a collection, not an array

                    // Dispatch the event to open the modal
                    $this->dispatch('show-attendees-modal');
                }

                public function resetPagination()
                {
                    $this->page = 1;
                    $this->hasMorePages = true;
                }

                public function render()
                {
                    return view('livewire.events', [
                        'events' => $this->events,
                        'hasMorePages' => $this->hasMorePages,
                    ])->extends('layouts.app');
                }
            }
