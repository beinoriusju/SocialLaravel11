<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Friend;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class UserSearch extends Component
{
    public $query = '';
    public $users = [];
    public $friendRequests = [];

    public function updatedQuery()
    {
        if (strlen($this->query) >= 2) {
            $this->users = User::where('username', 'like', '%' . $this->query . '%')
                               ->orWhere('last_name', 'like', '%' . $this->query . '%')
                               ->get();

            $this->loadFriendRequests();
        } else {
            $this->users = [];
        }
    }

    public function loadFriendRequests()
    {
        // Fetch all friend requests where the logged-in user is involved (sent or received)
        $this->friendRequests = Friend::where(function($query) {
            $query->where('user_id', auth()->id())   // Requests sent by logged-in user
                  ->orWhere('friend_id', auth()->id()); // Requests received by logged-in user
        })->get();
    }

    public function sendFriendRequest($receiverId)
    {
        DB::beginTransaction();
        try {
            $existingRequest = Friend::where(function ($query) use ($receiverId) {
                $query->where('user_id', auth()->id())
                      ->where('friend_id', $receiverId);
            })->orWhere(function ($query) use ($receiverId) {
                $query->where('friend_id', auth()->id())
                      ->where('user_id', $receiverId);
            })->first();

            if (!$existingRequest) {
                Friend::create([
                    'user_id' => auth()->id(),
                    'friend_id' => $receiverId,
                    'status' => 'pending',
                ]);

                Notification::create([
                    'type' => 'friend_request',
                    'sender_id' => auth()->id(),
                    'receiver_id' => $receiverId,
                    'message' => 'sent you a friend request.',
                    'url' => route('userprofile', ['user' => auth()->id()]),
                ]);

                DB::commit();

                $this->dispatch('alert', [
                    'type' => 'success',
                    'message' => __('translations.Friend request sent successfully'),
                ]);

                $this->loadFriendRequests();
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            $this->dispatch('alert', [
                'type' => 'error',
                'message' => __('translations.Something went wrong!'),
            ]);
        }
    }

    public function removeFriend($receiverId)
    {
        DB::beginTransaction();
        try {
            $friendship = Friend::where(function ($query) use ($receiverId) {
                $query->where('user_id', auth()->id())
                      ->where('friend_id', $receiverId);
            })->orWhere(function ($query) use ($receiverId) {
                $query->where('friend_id', auth()->id())
                      ->where('user_id', $receiverId);
            })->first();

            if ($friendship) {
                $status = $friendship->status;
                $friendship->delete();

                if ($status === 'pending') {
                    Notification::create([
                        'type' => 'friend_request_canceled',
                        'sender_id' => auth()->id(),
                        'receiver_id' => $receiverId,
                        'message' => 'canceled the friend request',
                        'url' => '#',
                    ]);
                } elseif ($status === 'accepted') {
                    Notification::create([
                        'type' => 'unfriend',
                        'sender_id' => auth()->id(),
                        'receiver_id' => $receiverId,
                        'message' => 'has unfriended you',
                        'url' => '#',
                    ]);
                }

                DB::commit();

                $this->dispatch('alert', [
                    'type' => 'success',
                    'message' => 'Friend request or friendship removed with ' . User::find($receiverId)->username,
                ]);

                $this->loadFriendRequests();
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            $this->dispatch('alert', [
                'type' => 'error',
                'message' => __('translations.Something went wrong!'),
            ]);
        }
    }

    public function acceptFriendRequest($userId)
    {
        Friend::where('user_id', $userId)
            ->where('friend_id', auth()->id())
            ->where('status', 'pending')
            ->update(['status' => 'accepted']);

        $this->loadFriendRequests();
    }

    public function render()
    {
        return view('livewire.user-search')->extends('layouts.app');
    }
}
