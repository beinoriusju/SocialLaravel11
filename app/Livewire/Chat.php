<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use App\Events\MessageSent;

class Chat extends Component
{
    use WithFileUploads;

    public $messages = [];
    public $conversations;
    public $selectedConversation = null;
    public $selectedUser = null;
    public $newMessage = '';
    public $attachments = [];
    public $selectedUserId = null;
    public $query = '';
    public $users = [];
    public $messageLimit = 10;

    protected $listeners = [
        'refreshMessages',
        'loadMoreMessages'
    ];

    public function mount()
    {
        $this->loadConversations();
        $this->loadMostRecentConversation();
    }

    public function loadConversations()
    {
        $this->conversations = Conversation::where('sender_id', Auth::id())
            ->orWhere('receiver_id', Auth::id())
            ->with('sender', 'receiver', 'messages')
            ->get()
            ->sortByDesc(function ($conversation) {
                return optional($conversation->messages->last())->created_at ?? $conversation->created_at;
            });
    }

    public function loadMostRecentConversation()
    {
        $this->selectedConversation = $this->conversations->first();
        if ($this->selectedConversation) {
            $this->setSelectedUser();
            $this->loadMessages();
        }
    }

    private function setSelectedUser()
    {
        if ($this->selectedConversation->sender_id === Auth::id()) {
            $this->selectedUser = $this->selectedConversation->receiver;
        } else {
            $this->selectedUser = $this->selectedConversation->sender;
        }
    }

    public function loadMessages()
    {
        if ($this->selectedConversation) {
            $this->messages = Message::where('conversation_id', $this->selectedConversation->id)
                ->orderBy('created_at', 'desc')
                ->take($this->messageLimit)
                ->with('sender', 'receiver')
                ->get()
                ->toArray();
        } else {
            $this->messages = [];
        }
    }

    public function loadMoreMessages()
    {
        $this->messageLimit += 10;
        $this->loadMessages();
    }

    public function conversationSelected($conversationId)
    {
        $this->selectedConversation = Conversation::with('messages', 'sender', 'receiver')
            ->find($conversationId);

        $this->setSelectedUser();
        $this->loadMessages();
    }

    public function sendMessage()
    {
        if (trim($this->newMessage) === '') { // Prevent sending empty messages
            session()->flash('error', 'Message cannot be empty.'); // Show an error message
            return;
        }

        if (!$this->selectedConversation) {
            if ($this->selectedUserId) {
                $selectedUser = User::find($this->selectedUserId);
                if (!$selectedUser) {
                    session()->flash('error', 'The selected user does not exist.');
                    return;
                }

                $this->selectedConversation = Conversation::firstOrCreate([
                    'sender_id' => Auth::id(),
                    'receiver_id' => $this->selectedUserId
                ]);

                $this->selectedUser = $selectedUser;
                $this->loadConversations();
            } else {
                session()->flash('error', 'Please select a user to chat with.');
                return;
            }
        }

        $messageData = [
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedUser->id,
            'body' => $this->newMessage,
        ];

        $message = Message::create($messageData);

        if ($this->attachments) {
            foreach ($this->attachments as $attachment) {
                $originalName = $attachment->getClientOriginalName();
                $filePath = $attachment->storeAs('attachments', $originalName, 'public');
                $fileType = $attachment->getMimeType();

                $message->update([
                    'file_path' => $filePath,
                    'file_type' => $fileType,
                    'file_name' => $originalName,
                ]);
            }
        }

        broadcast(new MessageSent($message))->toOthers();
        $this->dispatch('refreshMessages');
        $this->newMessage = ''; // Clear the input field after sending a message
    }

    public function selectUser($userId)
    {
        $this->selectedUserId = $userId;

        $existingConversation = Conversation::where(function ($query) use ($userId) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', Auth::id());
        })->first();

        if ($existingConversation) {
            $this->selectedConversation = $existingConversation;
            $this->conversationSelected($existingConversation->id);
        } else {
            $this->selectedConversation = null;
            $this->selectedUser = User::find($userId);
        }

        $this->loadConversations();
        $this->query = '';
    }

    public function refreshMessages()
    {
        $this->loadMessages();
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
        return view('livewire.chat')->extends('layouts.chat');
    }
}
