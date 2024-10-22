<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Friend;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use App\Events\NotificationSent; // Import the event class

class FriendsRequests extends Component
{
    use WithPagination;

    public $friendRequests = [];
    public $requestsPerPage = 10; // Number of requests to load per request

    public function mount()
    {
        $this->loadRequests();
    }

    public function loadRequests()
    {
        $this->friendRequests = Friend::where('friend_id', auth()->id())
            ->where('status', 'pending')
            ->limit($this->requestsPerPage)
            ->get();
    }

    public function loadMoreRequests()
    {
        $currentPage = ceil(count($this->friendRequests) / $this->requestsPerPage) + 1;
        $newRequests = Friend::where('friend_id', auth()->id())
            ->where('status', 'pending')
            ->paginate($this->requestsPerPage, ['*'], 'page', $currentPage);

        if ($newRequests->isEmpty()) {
            $this->dispatch('noMoreRequests');
        } else {
            $this->friendRequests = array_merge($this->friendRequests, $newRequests->items());
            $this->dispatch('requestsLoaded', ['hasMorePages' => $newRequests->hasMorePages()]);
        }
    }

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
                $notification = Notification::create([
                    'type' => 'friend_accepted',
                    'sender_id' => auth()->id(),
                    'receiver_id' => $userId,
                    'message' => auth()->user()->username .' accepted your friend request.',
                    'url' => '#',
                ]);

                // Broadcast the notification
                broadcast(new NotificationSent($notification))->toOthers();

                DB::commit();
                $this->loadRequests(); // Refresh the requests list after accepting
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

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
                $notification = Notification::create([
                    'type' => 'friend_rejected',
                    'sender_id' => auth()->id(),
                    'receiver_id' => $userId,
                    'message' => auth()->user()->username . ' rejected your friend request.',
                    'url' => '#',
                ]);

                // Broadcast the notification
                broadcast(new NotificationSent($notification))->toOthers();

                DB::commit();
                $this->loadRequests(); // Refresh the requests list after rejecting
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function render()
    {
        return view('livewire.friends-requests', [
            'friendRequests' => $this->friendRequests,
        ])->extends('layouts.app');
    }
}
