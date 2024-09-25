<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Friend;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class Friends extends Component
{
    use WithPagination;

    public $friends = [];
    public $friendsPerPage = 20; // Number of friends to load per request

    public function mount()
    {
        $this->loadFriends();
    }

    public function loadFriends()
    {
        $this->friends = Friend::where(function($query) {
            $query->where('user_id', auth()->id())
                  ->orWhere('friend_id', auth()->id());
        })
        ->where('status', 'accepted')
        ->limit($this->friendsPerPage)
        ->get();
    }

    public function loadMoreFriends()
    {
        $currentPage = ceil(count($this->friends) / $this->friendsPerPage) + 1;
        $newFriends = Friend::where(function($query) {
            $query->where('user_id', auth()->id())
                  ->orWhere('friend_id', auth()->id());
        })
        ->where('status', 'accepted')
        ->paginate($this->friendsPerPage, ['*'], 'page', $currentPage);

        if ($newFriends->isEmpty()) {
            $this->dispatch('noMoreFriends');
        } else {
            $this->friends = array_merge($this->friends, $newFriends->items());
            $this->dispatch('friendsLoaded', ['hasMorePages' => $newFriends->hasMorePages()]);
        }
    }

    public function unfriend($userId)
    {
        DB::beginTransaction();
        try {
            $friendship = Friend::where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->where('friend_id', auth()->id());
            })->orWhere(function ($query) use ($userId) {
                $query->where('user_id', auth()->id())
                      ->where('friend_id', $userId);
            })->where('status', 'accepted')->first();

            if ($friendship) {
                $friendship->delete(); // Unfriend logic: delete the friendship

                // Send a notification to the unfriended user
                Notification::create([
                    'type' => 'unfriend',
                    'sender_id' => auth()->id(), // The logged-in user who unfriended
                    'receiver_id' => $userId, // Notify the unfriended user
                    'message' => 'has unfriended you.',
                    'url' => '#',
                ]);

                DB::commit();
                session()->flash('message', 'You have unfriended this user.');
                $this->loadFriends(); // Refresh the friends list after unfriending
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', 'Failed to unfriend this user.');
            throw $th;
        }
    }

    public function render()
    {
        return view('livewire.friends', [
            'friends' => $this->friends,
        ])->extends('layouts.app');
    }
}
