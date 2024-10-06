<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Friend;
use App\Notifications\CustomResetPassword;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // public function sendPasswordResetNotification($token)
    // {
    //     $this->notify(new CustomResetPassword($token));
    // }

    // Get all of the likes for the user
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Relationship to the Friend model (where user is the sender)
    public function sentFriendRequests()
    {
        return $this->hasMany(Friend::class, 'user_id', 'id');
    }

    // Relationship to the Friend model (where user is the recipient)
    public function receivedFriendRequests()
    {
        return $this->hasMany(Friend::class, 'friend_id', 'id');
    }

    // Check if the current user is friends with another user
    public function is_friend($user_id)
    {
        return Friend::where(function ($query) use ($user_id) {
            $query->where('user_id', $this->id)
                ->where('friend_id', $user_id)
                ->where('status', 'accepted');
        })->orWhere(function ($query) use ($user_id) {
            $query->where('friend_id', $this->id)
                ->where('user_id', $user_id)
                ->where('status', 'accepted');
        })->exists();
    }

    // Get a list of friends for the user
    public function friends()
    {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')
            ->wherePivot('status', 'accepted')
            ->orWherePivot('user_id', $this->id)
            ->orWherePivot('friend_id', $this->id)
            ->withTimestamps();
    }

    public function conversations()
   {
       return $this->hasMany(Conversation::class, 'sender_id')
           ->orWhere('receiver_id', $this->id);
   }

   // Relationship to messages (the user can be the sender or receiver)
   public function messages()
   {
       return $this->hasMany(Message::class, 'sender_id')
           ->orWhere('receiver_id', $this->id);
   }
}
