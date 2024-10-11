<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ConversationList extends Component
{
    public $conversations;
    public $query = '';
    public $users = [];

    public function mount()
    {
        $this->loadConversations();
    }

    public function loadConversations()
    {
        $this->conversations = Conversation::where('sender_id', Auth::id())
            ->orWhere('receiver_id', Auth::id())
            ->with('sender', 'receiver', 'messages')
            ->get()
            ->map(function ($conversation) {
                $conversation->hasUnreadMessages = $conversation->messages->where('receiver_id', Auth::id())->whereNull('read_at')->count() > 0;
                return $conversation;
            })
            ->sortByDesc(function ($conversation) {
                return optional($conversation->messages->last())->created_at ?? $conversation->created_at;
            });
    }

    public function selectUser($userId)
    {
        // Check if a conversation already exists
        $existingConversation = Conversation::where(function ($query) use ($userId) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', Auth::id());
        })->first();

        // If the conversation exists, redirect to the chat
        if ($existingConversation) {
            return redirect()->route('chat.show', ['id' => $existingConversation->id]);
        } else {
            // If no conversation exists, create a new one and then redirect
            $conversation = Conversation::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $userId,
            ]);

            return redirect()->route('chat.show', ['id' => $conversation->id]);
        }
    }

    public function updatedQuery()
    {
        if (strlen($this->query) >= 2) {
            $this->users = User::where('username', 'like', '%' . $this->query . '%')->get();
        } else {
            $this->users = [];
        }
    }

    public function render()
    {
        return view('livewire.conversation-list')->extends("layouts.chat");
    }
}
