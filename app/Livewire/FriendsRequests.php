<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Friend;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class FriendsRequests extends Component
{
    use WithPagination;

    // Accept a friend request
    public function acceptFriend($userId)
    {
        DB::beginTransaction();
        try {
            $friendRequest = Friend::where('user_id', $userId)
                                    ->where('friend_id', auth()->id())
                                    ->where('status', 'pending')
                                    ->first();

            if ($friendRequest) {
                $friendRequest->update(['status' => 'accepted']);

                // Notify the user that their request was accepted
                Notification::create([
                    'type' => 'friend_accepted',
                    'sender_id' => auth()->id(),
                    'receiver_id' => $userId,
                    'message' => 'accepted your friend request.',
                    'url' => '#',
                ]);

                DB::commit();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    // Reject a friend request
    public function rejectFriend($userId)
    {
        DB::beginTransaction();
        try {
            $friendRequest = Friend::where('user_id', $userId)
                                    ->where('friend_id', auth()->id())
                                    ->where('status', 'pending')
                                    ->first();

            if ($friendRequest) {
                $friendRequest->delete();

                // Notify the user that their request was rejected
                Notification::create([
                    'type' => 'friend_rejected',
                    'sender_id' => auth()->id(),
                    'receiver_id' => $userId,
                    'message' => 'rejected your friend request.',
                    'url' => '#',
                ]);

                DB::commit();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    // Render the view
    public function render()
    {
        // Fetch pending friend requests
        $friendRequests = Friend::where('friend_id', auth()->id())
                                ->where('status', 'pending')
                                ->paginate(10);

        return view('livewire.friends-requests', [
            'friendRequests' => $friendRequests,
        ])->extends('layouts.app');
    }
}
