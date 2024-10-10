<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notification;
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
        $this->unreadCount = Notification::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();
    }

    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}
