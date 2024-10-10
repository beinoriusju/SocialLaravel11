<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class UnreadMessagesBadge extends Component
{
    public $unreadCount = 0;

    protected $listeners = ['refreshUnreadMessages' => 'loadUnreadMessages'];

    public function mount()
    {
        $this->loadUnreadMessages();
    }

    public function loadUnreadMessages()
    {
        // Count unread messages for the authenticated user
        $this->unreadCount = Message::where('receiver_id', Auth::id())
            ->whereNull('read_at') // Check for unread messages
            ->count();
    }

    public function render()
    {
        return view('livewire.unread-messages-badge', [
            'unreadCount' => $this->unreadCount,
        ]);
    }
}
