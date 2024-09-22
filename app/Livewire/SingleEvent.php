<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Event;

class SingleEvent extends Component
{
    public $event;

    public function mount($event)
    {
        // Retrieve the event by its ID
        $this->event = Event::with('media')->findOrFail($event);
    }

    public function render()
    {
        return view('livewire.single-event', [
            'event' => $this->event,
        ])->extends('layouts.app');
    }
}
