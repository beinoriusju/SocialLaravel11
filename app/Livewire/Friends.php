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

    // Unfriend logic with notification
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
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', 'Failed to unfriend this user.');
            throw $th;
        }

        $this->render(); // Refresh the friends list after unfriending
    }

    // Fetch friends and handle pagination
    public function render()
    {
        // Fetch accepted friends
        $friends = Friend::where(function($query) {
            $query->where('user_id', auth()->id())   // Friends of the logged-in user
                  ->orWhere('friend_id', auth()->id());
        })
        ->where('status', 'accepted')
        ->paginate(20);

        return view('livewire.friends', [
            'friends' => $friends,
        ])->extends('layouts.app');
    }
}
