<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use App\Events\MessageSent;
use App\Events\MessageDeleted;
use App\Jobs\DeleteExpiredFiles;
use Illuminate\Support\Str;

class Chat extends Component
{
    use WithFileUploads;

    public $messages = [];
    public $conversation;
    public $selectedUser = null;
    public $newMessage = '';
    public $attachments = [];
    public $messageLimit = 10;

    protected $listeners = [
        'refreshMessages',
        'loadMoreMessages',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $this->conversation = Conversation::with('messages', 'sender', 'receiver')->find($id);
            if ($this->conversation) {
                $this->setSelectedUser();
                $this->markMessagesAsRead();
                $this->loadMessages();
            }
        }
    }

    // Set the selected user for the chat
    public function setSelectedUser()
    {
        if ($this->conversation->sender_id === Auth::id()) {
            $this->selectedUser = $this->conversation->receiver;
        } else {
            $this->selectedUser = $this->conversation->sender;
        }
    }

    // Mark unread messages as read
    public function markMessagesAsRead()
    {
        Message::where('conversation_id', $this->conversation->id)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    // Load messages for the conversation
    public function loadMessages()
    {
        $this->messages = Message::where('conversation_id', $this->conversation->id)
            ->orderBy('created_at', 'desc')
            ->take($this->messageLimit)
            ->with('sender', 'receiver')
            ->get()
            ->toArray();
    }

    // Load more messages when scrolling
    public function loadMoreMessages()
    {
        $this->messageLimit += 10;
        $this->loadMessages();
    }

    // Send a new message with optional file attachments and YouTube links
    public function sendMessage()
    {
        if (trim($this->newMessage) === '' && empty($this->attachments)) {
            return;
        }

        // Extract YouTube links from the message
        $youtubeLinks = $this->extractYouTubeLinks($this->newMessage);

        // Create a new message
        $messageData = [
            'conversation_id' => $this->conversation->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedUser->id,
            'body' => $this->newMessage,
        ];

        $message = Message::create($messageData);

        // Handle file uploads and YouTube links
        if ($this->attachments || !empty($youtubeLinks)) {
            $this->validate([
                'attachments.*' => 'file|max:51200000', // Max 50 MB per file
            ]);

            $filePaths = [];

            // Handle file attachments
            if ($this->attachments) {
                foreach ($this->attachments as $attachment) {
                    $filePath = $attachment->store('attachments', 'public');
                    $filePaths[] = $filePath;
                }
            }

            // Add YouTube links as file paths
            foreach ($youtubeLinks as $link) {
                $filePaths[] = $link; // Store YouTube links directly as strings
            }

            // Update the message with the file paths
            $message->update([
                'file_path' => implode(',', $filePaths), // Save as a comma-separated string instead of JSON
                'file_type' => !empty($youtubeLinks) ? 'youtube' : 'file', // Mark as YouTube or file
            ]);

            // Schedule file deletion after 55 minutes (for file attachments only)
            if ($this->attachments) {
                DeleteExpiredFiles::dispatch($message)->delay(now()->addMinutes(55));
            }
        }

        // Broadcast the new message event for real-time updates
        broadcast(new MessageSent($message))->toOthers();

        // Reset the input fields
        $this->resetForm();

        // Emit the refresh messages event so the frontend updates immediately
        $this->dispatch('refreshMessages');
    }

    // Reset form fields after sending a message
    private function resetForm()
    {
        $this->newMessage = '';
        $this->attachments = [];
        $this->dispatch('refreshMessages');
    }

    // Refresh messages
    public function refreshMessages()
    {
        $this->loadMessages();
    }

    // Delete the entire conversation and its messages
    public function deleteConversation()
    {
        foreach ($this->conversation->messages as $message) {
            $message->delete();
        }

        $this->conversation->delete();

        // Redirect back to the conversation list
        return redirect()->route('conversations');
    }

    // Delete a single message
    public function deleteMessage($messageId)
    {
        $message = Message::find($messageId);

        if ($message && $message->sender_id == Auth::id()) {
            $message->delete();
            $this->loadMessages();

            // Broadcast the message deletion event
            broadcast(new MessageSent($message, true))->toOthers();
            $this->dispatch('refreshMessages');

        }
    }

    // Extract YouTube links from the message body
    protected function extractYouTubeLinks($message)
    {
        if (!$message) {
            return [];
        }

        $videoPattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w\-]+)/i';
        $playlistPattern = '/(?:https?:\/\/)?(?:youtube\.com\/playlist\?list=)([\w\-]+)/i';

        $matches = [];
        preg_match_all($videoPattern, $message, $videoMatches);
        preg_match_all($playlistPattern, $message, $playlistMatches);

        return array_merge($videoMatches[0], $playlistMatches[0]);
    }

    // Go back to the conversation list
    public function goBackToConversationList()
    {
        return redirect()->route('conversations');
    }

    // Render the chat view
    public function render()
    {
        return view('livewire.chat')->extends('layouts.chat');
    }
}
