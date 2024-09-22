<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationDropdown extends Component
{
    public $notifications;
    public $unreadCount;

    protected $listeners = ['notificationsUpdated' => '$refresh'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        // Load only unread notifications where the logged-in user is the receiver
        $this->notifications = Notification::where('receiver_id', Auth::id())
                                           ->whereNull('read_at')
                                           ->latest()
                                           ->take(5)  // You can adjust the limit as per your need
                                           ->get();

        // Count all unread notifications for the logged-in user
        $this->unreadCount = Notification::where('receiver_id', Auth::id())
                                         ->whereNull('read_at')
                                         ->count();
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification && $notification->receiver_id == Auth::id()) {
            $notification->update(['read_at' => now()]);
        }

        // Reload notifications after marking one as read
        $this->loadNotifications();
        $this->dispatch('notificationsUpdated');
    }

    public function markAllAsRead()
    {
        Notification::where('receiver_id', Auth::id())
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);

        // Reload notifications after marking all as read
        $this->loadNotifications();
        $this->dispatch('notificationsUpdated');
    }

    public function render()
    {
        return view('livewire.notification-dropdown', [
            'notifications' => $this->notifications,
            'unreadCount' => $this->unreadCount,
        ]);
    }
}
