<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Friend;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Events\NotificationSent; // Import the event class

class UsersList extends Component
{
    use WithPagination;

    public $users = []; // Array to hold user data
    public $usersPerPage = 20; // Number of users to load per request

    public function mount()
    {
        $this->loadUsers();
    }

    public function loadUsers()
    {
        $this->users = User::limit($this->usersPerPage)->get();
    }

    public function loadMoreUsers()
    {
        $currentPage = ceil(count($this->users) / $this->usersPerPage) + 1;

        // Fetch the new users
        $newUsers = User::paginate($this->usersPerPage, ['*'], 'page', $currentPage);

        // If no new users are returned, dispatch the "noMoreUsers" event
        if ($newUsers->isEmpty()) {
            $this->dispatch('noMoreUsers');  // Emit event if no more users
        } else {
            // Append new users to the current users array
            $this->users = array_merge($this->users, $newUsers->items());

            // Check if there are more pages to load
            if (!$newUsers->hasMorePages()) {
                $this->dispatch('noMoreUsers');
            } else {
                $this->dispatch('usersLoaded', ['hasMorePages' => $newUsers->hasMorePages()]);
            }
        }
    }

    public function acceptFriend($id)
    {
        $user = User::find($id);

        DB::beginTransaction();
        try {
            $req = Friend::where([
                'user_id' => $id,
                'friend_id' => auth()->id(),
                'status' => 'pending',
            ])->first();

            if ($req) {
                $req->status = 'accepted';
                $req->save();

                $notification = Notification::create([
                    'type' => 'friend_accepted',
                    'sender_id' => auth()->id(),
                    'receiver_id' => $user->id,
                    'message' => auth()->user()->username . ' accepted your friend request',
                    'url' => '#',
                ]);

                // Broadcast the notification
                broadcast(new NotificationSent($notification))->toOthers();

                DB::commit();
                $this->dispatch('alert', [
                    'type' => 'success', 'message' => 'Friend request accepted'
                ]);

                // Reload the page after accepting the friend request
                $this->dispatch('reload');
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

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
            Friend::create([
                'user_id' => auth()->id(),
                'friend_id' => $user->id,
                'status' => 'pending',
            ]);

            $notification = Notification::create([
                'type' => 'friend_request',
                'sender_id' => auth()->id(),
                'receiver_id' => $user->id,
                'message' => auth()->user()->username . ' sent you a friend request',
                'url' => '#',
            ]);

            // Broadcast the notification
            // broadcast(new NotificationSent($notification))->toOthers();

            DB::commit();
            $this->dispatch('alert', [
                'type' => 'success', 'message' => 'Friend request sent to ' . $user->username
            ]);

            // Reload the page after sending the friend request
            $this->dispatch('reload');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function removeFriend($id)
    {
        $user = User::find($id);

        DB::beginTransaction();
        try {
            $friendship = Friend::where(function ($query) use ($user) {
                $query->where('user_id', auth()->id())
                      ->where('friend_id', $user->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('friend_id', auth()->id())
                      ->where('user_id', $user->id);
            })->first();

            if ($friendship) {
                $status = $friendship->status;
                $friendship->delete();

                // Initialize the $notification variable to null
                $notification = null;

                if ($status === 'pending') {
                    $notification = Notification::create([
                        'type' => 'friend_request_canceled',
                        'sender_id' => auth()->id(),
                        'receiver_id' => $user->id,
                        'message' => auth()->user()->username . ' canceled the friend request',
                        'url' => '#',
                    ]);
                } elseif ($status === 'accepted') {
                    $notification = Notification::create([
                        'type' => 'unfriend',
                        'sender_id' => auth()->id(),
                        'receiver_id' => $user->id,
                        'message' => auth()->user()->username . ' has unfriended you',
                        'url' => '#',
                    ]);
                }

                // Check if a notification was created and broadcast it
                if ($notification) {
                    broadcast(new NotificationSent($notification))->toOthers();
                }

                DB::commit();
                $this->dispatch('alert', [
                    'type' => 'success', 'message' => 'Friend request or friendship removed with ' . $user->username
                ]);

                // Reload the page after removing a friend
                $this->dispatch('reload');
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function deleteUser($id)
    {
        $this->confirmDeleteUser($id);
    }

    public function confirmDeleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            $this->dispatch('alert', [
                'type' => 'error', 'message' => 'User not found'
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            // Delete associated files
            $this->deleteUserFiles($user);

            // Delete the user
            $user->delete();

            DB::commit();

            $this->dispatch('alert', [
                'type' => 'success', 'message' => 'User deleted successfully'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('alert', [
                'type' => 'error', 'message' => 'Failed to delete user'
            ]);
            throw $th;
        }
    }

    public function deleteUserFiles($user)
    {
        if ($user) {
            // Use the public disk for publicly accessible files
            $profileImageDirectory = "profileImages/{$user->id}";
            $postsImagesDirectory = "posts/{$user->id}/images";
            $postsVideosDirectory = "posts/{$user->id}/videos";
            $eventsImagesDirectory = "events/{$user->id}/images";
            $eventsVideosDirectory = "events/{$user->id}/videos";

            // Delete profile image directory
            if (Storage::disk('public')->exists($profileImageDirectory)) {
                Storage::disk('public')->deleteDirectory($profileImageDirectory);
            }

            // Delete posts images directory
            if (Storage::disk('public')->exists($postsImagesDirectory)) {
                Storage::disk('public')->deleteDirectory($postsImagesDirectory);
            }

            // Delete posts videos directory
            if (Storage::disk('public')->exists($postsVideosDirectory)) {
                Storage::disk('public')->deleteDirectory($postsVideosDirectory);
            }

            // Delete events images directory
            if (Storage::disk('public')->exists($eventsImagesDirectory)) {
                Storage::disk('public')->deleteDirectory($eventsImagesDirectory);
            }

            // Delete events videos directory
            if (Storage::disk('public')->exists($eventsVideosDirectory)) {
                Storage::disk('public')->deleteDirectory($eventsVideosDirectory);
            }
        }
    }

    public function render()
    {
        return view('livewire.users-list', [
            'users' => $this->users,
            'friendRequests' => Friend::where(function ($query) {
                $query->where('user_id', auth()->id())
                      ->orWhere('friend_id', auth()->id());
            })->get(),
        ])->extends('layouts.app');
    }
}
