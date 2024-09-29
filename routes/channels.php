<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    // Ensure the user is part of the conversation
    return \App\Models\Conversation::where('id', $conversationId)
        ->where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
        })->exists();
});

Broadcast::channel('user-selected.{userId}', function ($user, $userId) {
    return $user->id === (int) $userId;
});

Broadcast::channel('conversation-selected.{conversationId}', function ($user, $conversationId) {
    // Authorization logic for conversation selection
    return \App\Models\Conversation::where('id', $conversationId)
        ->where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
        })->exists();
});
?>
