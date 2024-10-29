<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Auth;

class NotificationDropdown extends Component
{
    public $unreadCount = 0;

    protected $listeners = ['notificationsUpdated' => 'loadUnreadCount'];

    public function mount()
    {
        $this->loadUnreadCount();
    }

    public function loadUnreadCount()
    {
        try {
            if (Auth::check()) {
                $userId = Auth::id();
                $this->unreadCount = Notification::where('receiver_id', $userId)
                    ->whereNull('read_at')
                    ->count();
            } else {
                $this->unreadCount = 0;
            }
        } catch (\Exception $e) {
            \Log::error('Error in loadUnreadCount: ' . $e->getMessage());
            $this->unreadCount = 0; // Default to 0 in case of an error
        }
    }

    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}
