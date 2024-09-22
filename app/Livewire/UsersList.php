<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Friend;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class UsersList extends Component
{
    use WithPagination;

    // Function to accept a friend request
    public function acceptFriend($id)
    {
        $user = User::find($id);

        DB::beginTransaction();
        try {
            $req = Friend::where([
                'user_id' => $id, // User who sent the request
                'friend_id' => auth()->id(),
                'status' => 'pending', // Ensure it's a pending request
            ])->first();

            if ($req) {
                $req->status = 'accepted'; // Mark the request as accepted
                $req->save();

                Log::info('Friend request accepted, creating notification'); // Add log for debugging

                // Create notification for accepted friend request
                Notification::create([
                    'type' => 'friend_accepted',
                    'sender_id' => auth()->id(), // The logged-in user who accepted the request
                    'receiver_id' => $user->id, // Notify the requester
                    'message' => 'accepted your friend request',
                    'url' => '#',
                ]);

                DB::commit();

                Log::info('Transaction committed successfully'); // Add log to confirm transaction success

                // Dispatch browser event for success
                $this->dispatch('alert', [
                    'type' => 'success', 'message' => 'Friend request accepted'
                ]);
            } else {
                Log::error('Friend request not found or is not pending'); // Log error if not found
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Transaction failed: ' . $th->getMessage()); // Log any error that occurs
            throw $th;
        }
    }

    // Function to add a new friend (send a friend request)
    public function addFriend($id)
    {
        $user = User::find($id);

        $existingRequest = Friend::where(function ($query) use ($user) {
            $query->where('user_id', auth()->id())
                  ->where('friend_id', $user->id)
                  ->where('status', 'pending');
        })->orWhere(function ($query) use ($user) {
            $query->where('friend_id', auth()->id())
                  ->where('user_id', $user->id)
                  ->where('status', 'pending');
        })->first();

        if ($existingRequest) {
            $this->dispatch('alert', [
                'type' => 'error', 'message' => 'Friend request is already pending'
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            Log::info('Sending friend request'); // Debug log for friend request sending

            // Create the friend request
            Friend::create([
                'user_id' => auth()->id(),
                'friend_id' => $user->id,
                'status' => 'pending',
            ]);

            // Create notification for friend request
            Notification::create([
                'type' => 'friend_request',
                'sender_id' => auth()->id(), // The logged-in user who is sending the request
                'receiver_id' => $user->id, // Notify the receiver of the request
                'message' => 'sent you a friend request',
                'url' => '#',
            ]);

            DB::commit();
            Log::info('Friend request and notification created successfully'); // Log success

            $this->dispatch('alert', [
                'type' => 'success', 'message' => 'Friend request sent to ' . $user->name
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Transaction failed: ' . $th->getMessage()); // Log any error that occurs
            throw $th;
        }
    }

    // Function to remove or cancel a friend request (or unfriend)
    public function removeFriend($id)
    {
        $user = User::find($id);

        DB::beginTransaction();
        try {
            // Find existing friendship or pending request
            $friendship = Friend::where(function ($query) use ($user) {
                $query->where('user_id', auth()->id())
                      ->where('friend_id', $user->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('friend_id', auth()->id())
                      ->where('user_id', $user->id);
            })->first();

            if ($friendship) {
                $status = $friendship->status; // Capture the status before deleting
                $friendship->delete(); // Delete the friendship

                Log::info('Friend request/friendship removed, creating notification'); // Debug log

                // Send notification based on the status
                if ($status === 'pending') {
                    Notification::create([
                        'type' => 'friend_request_canceled',
                        'sender_id' => auth()->id(), // The logged-in user who canceled the request
                        'receiver_id' => $user->id, // Notify the other party
                        'message' => 'canceled the friend request',
                        'url' => '#',
                    ]);
                } elseif ($status === 'accepted') {
                    Notification::create([
                        'type' => 'unfriend',
                        'sender_id' => auth()->id(), // The logged-in user who unfriended
                        'receiver_id' => $user->id, // Notify the other party
                        'message' => 'has unfriended you',
                        'url' => '#',
                    ]);
                }

                DB::commit();
                Log::info('Notification for friend request removal created successfully'); // Log success

                $this->dispatch('alert', [
                    'type' => 'success', 'message' => 'Friend request or friendship removed with ' . $user->name
                ]);
            } else {
                Log::error('No existing friendship or request found'); // Log error if not found
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Transaction failed: ' . $th->getMessage()); // Log any error that occurs
            throw $th;
        }
    }

    // Pass additional friend request data to the Blade view
    public function render()
    {
        // Get all users
        $users = User::paginate(20);

        // Fetch all friend requests where the logged-in user is involved (sent or received)
        $friendRequests = Friend::where(function($query) {
            $query->where('user_id', auth()->id())   // Requests sent by logged-in user
                  ->orWhere('friend_id', auth()->id()); // Requests received by logged-in user
        })->get();

        return view('livewire.users-list', [
            'users' => $users,
            'friendRequests' => $friendRequests  // Pass friend requests to the Blade view
        ])->extends('layouts.app');
    }
}
