<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\EventCategory;
use App\Models\EventSubCategory; 
use App\Models\EventAttendee;

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
    public $video;
    public $editMode = false;
    public $eventIdBeingEdited = null;

    public $startDate; // Start date for filtering
    public $endDate;   // End date for filtering
    public $attendees = []; // Store attendees for modal

    public function mount()
    {
        // Fetch all categories with subcategories
        $this->categories = EventCategory::with('eventSubCategories')->get();
    }

    public function updatedSelectedCategory($categoryId)
    {
        if ($categoryId) {
            $this->subcategories = EventSubCategory::where('category_id', $categoryId)->get();
        } else {
            $this->subcategories = [];
        }

        $this->selectedSubcategory = null;
    }

    public function createEvent()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'details' => 'nullable|string',
            'event_date' => 'required|date',
            'images.*' => 'nullable|image|max:51200',
            'video' => 'nullable|mimes:mp4,mkv|max:51200',
        ]);

        Event::create([
            'title' => $this->title,
            'description' => $this->description,
            'details' => $this->details,
            'event_date' => $this->event_date,
            'category_id' => $this->selectedCategory,
            'subcategory_id' => $this->selectedSubcategory,
            'user_id' => auth()->id(),
        ]);

        // Reset form after submission
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
            'video' => 'nullable|mimes:mp4,mkv|max:51200',
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

        $this->resetForm();
    }

    public function deleteEvent($eventId)
    {
        $event = Event::findOrFail($eventId);

        // Get associated media
        $mediaItems = $event->media;

        // Loop through each media item and delete it from storage
        foreach ($mediaItems as $media) {
            if ($media->file_type == 'image' || $media->file_type == 'video') {
                \Storage::disk('public')->delete($media->file);
            }
            $media->delete();
        }

        // Delete the event itself
        $event->delete();
    }

    public function resetForm()
    {
        $this->editMode = false;
        $this->title = '';
        $this->description = '';
        $this->details = '';
        $this->event_date = '';
        $this->images = [];
        $this->video = '';
        $this->selectedCategory = null;
        $this->selectedSubcategory = null;
        $this->eventIdBeingEdited = null;
    }

    public function filterEvents()
    {
        $this->resetPage();
    }

    /**
     * Load attendees for a specific event and show them in a modal.
     */
    public function showAttendees($eventId)
    {
        $event = Event::with('attendees')->findOrFail($eventId);
        $this->attendees = $event->attendees;
    }

    public function toggleAttendance($eventId)
    {
        $userId = auth()->id();
        $attendee = EventAttendee::where('event_id', $eventId)->where('user_id', $userId)->first();

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
    }

    public function render()
    {
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

        $events = $query->latest()->paginate(10);

        return view('livewire.event', [
            'events' => $events,
        ])->extends('layouts.app');
    }
}
