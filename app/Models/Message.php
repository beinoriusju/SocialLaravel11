<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    // Allow all fields to be mass-assigned (use guarded to prevent this)
    protected $guarded = [];

    // Automatically cast these columns to dates
    protected $dates = ['read_at', 'receiver_deleted_at', 'sender_deleted_at', 'deleted_at'];

    // Encrypt the body of the message
    public function setBodyAttribute($value)
    {
        $this->attributes['body'] = Crypt::encryptString($value);
    }

    public function getBodyAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    // Relationships

    /**
     * Belongs to a conversation
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the sender of the message
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the message
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Check if the message has been read
     */
    public function isRead()
    {
        return $this->read_at !== null;
    }

    /**
     * Check if the message has been deleted for the given user
     */
    public function isDeletedByUser($userId)
    {
        return $this->sender_deleted_at !== null && $this->sender_id == $userId || $this->receiver_deleted_at !== null && $this->receiver_id == $userId;
    }
}
