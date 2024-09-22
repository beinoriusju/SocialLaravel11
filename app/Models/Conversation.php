<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    // Use guarded to allow mass assignment for all fields except specified
    protected $guarded = [];

    // Define relationships

    /**
     * Get the sender of the conversation.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the conversation.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get all messages in the conversation.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Check if the conversation is soft deleted for one of the users.
     */
    public function isDeletedByUser($userId)
    {
        return $this->trashed() && ($this->sender_id === $userId || $this->receiver_id === $userId);
    }
}
