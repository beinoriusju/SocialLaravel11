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

    public $messages = [];  // Must be an array, not a collection
    public $conversations;
    public $selectedConversation = null;
    public $selectedUser = null;
    public $newMessage = '';
    public $attachments = [];
    public $selectedUserId = null;
    public $query = '';
    public $users = [];
    public $messageLimit = 10;  // Limit for messages initially loaded

    protected $listeners = [
        'refreshMessages', // For real-time updates
        'submitMessage' => 'sendMessage', // Custom listener for "Enter" key submission
        'loadMoreMessages' => 'loadMoreMessages' // Listener for infinite scrolling
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
                ->orderBy('created_at', 'asc')
                ->take($this->messageLimit) // Load messages based on the limit
                ->with('sender', 'receiver') // Eager load users
                ->get()
                ->toArray();  // Convert the collection to an array
        } else {
            $this->messages = [];
        }
    }

    public function loadMoreMessages()
    {
        // Increase the message limit and reload messages
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
        // Ensure there is either a new message or attachments before proceeding
        if (!$this->newMessage && empty($this->attachments)) {
            return; // Exit if there's nothing to send
        }

        // Handle conversation creation if none is selected
        if (!$this->selectedConversation) {
            if ($this->selectedUserId) {
                $selectedUser = User::find($this->selectedUserId);
                if (!$selectedUser) {
                    session()->flash('error', 'The selected user does not exist.');
                    return; // Exit if the selected user does not exist
                }

                // Create or get existing conversation
                $this->selectedConversation = Conversation::firstOrCreate([
                    'sender_id' => Auth::id(),
                    'receiver_id' => $this->selectedUserId
                ]);

                $this->selectedUser = $selectedUser; // Set the selected user
                $this->loadConversations(); // Refresh conversation list
            } else {
                session()->flash('error', 'Please select a user to chat with.');
                return; // Exit if no user is selected
            }
        }

        // Prepare the message data
        $messageData = [
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedUser->id,
            'body' => $this->newMessage,
        ];

        // Create the message
        $message = Message::create($messageData);

        // Handle file uploads
        if ($this->attachments) {
            foreach ($this->attachments as $attachment) {
                $originalName = $attachment->getClientOriginalName();
                $filePath = $attachment->storeAs('attachments', $originalName, 'public');
                $fileType = $attachment->getMimeType();

                // Associate the file with the message
                $message->update([
                    'file_path' => $filePath,
                    'file_type' => $fileType,
                    'file_name' => $originalName,
                ]);
            }
        }

        // Clear the input fields
        $this->newMessage = '';
        $this->attachments = [];

        // Broadcast the message using Pusher
        broadcast(new MessageSent($message))->toOthers();

        // Emit to refresh the messages
        $this->dispatch('refreshMessages');
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

        // Clear the search query to close the dropdown
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
            \Log::info('Search Query: ' . $this->query);
            \Log::info('Search Results: ', $this->users->toArray());
        } else {
            $this->users = [];
        }
    }

    public function render()
    {
        return view('livewire.chat')->extends('layouts.chat');
    }
}
