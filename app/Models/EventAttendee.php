<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAttendee extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'user_id'];

    /**
     * Get the event that the attendee is attending.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who is attending the event.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
