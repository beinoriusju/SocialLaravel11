<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class Notifications extends Component
{
    public $notifications = [];
    public $unreadCount = 0;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = Notification::where('receiver_id', Auth::id())
            ->latest()
            ->get();

        $this->unreadCount = Notification::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification && $notification->receiver_id == Auth::id()) {
            $notification->delete(); // Delete the notification from the database
        }

        // Refresh notifications after deletion
        $this->loadNotifications(); // Reload notifications
        $this->dispatch('reload');
    }

    public function markAllAsRead()
    {
        Notification::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->delete(); // Delete all unread notifications
        $this->loadNotifications(); // Refresh notifications
    }

    public function render()
    {
        return view('livewire.notifications', [
            'notifications' => $this->notifications,
            'unreadCount' => $this->unreadCount,
        ])->extends('layouts.app');
    }
}
