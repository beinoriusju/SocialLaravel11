<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $dates = ['read_at', 'receiver_deleted_at', 'sender_deleted_at'];

    // Encrypt the body of the message
    public function setBodyAttribute($value)
    {
        $this->attributes['body'] = Crypt::encryptString($value);
    }

    public function getBodyAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($message) {
            // Delete associated files when a message is deleted
            if ($message->file_path) {
                $filePaths = json_decode($message->file_path, true);

                if (is_array($filePaths)) {
                    foreach ($filePaths as $filePath) {
                        if (Storage::disk('public')->exists($filePath)) {
                            Storage::disk('public')->delete($filePath);
                        }
                    }
                }
            }
        });
    }

    // Relationships

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function isRead()
    {
        return $this->read_at !== null;
    }

    public function isDeletedByUser($userId)
    {
        return ($this->sender_deleted_at && $this->sender_id == $userId) || ($this->receiver_deleted_at && $this->receiver_id == $userId);
    }
}
