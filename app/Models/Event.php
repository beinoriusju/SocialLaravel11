<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    // Guarded attributes for mass assignment, allowing all fields except those explicitly guarded
    protected $guarded = [];

    /**
     * Cast attributes to native types
     *
     * @var array
     */
    protected $casts = [
        'event_date' => 'date',  // Cast event_date as a Carbon instance
    ];

    /**
     * Get the user that owns the event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the category that the event belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    /**
     * Get the subcategory that the event belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(EventSubCategory::class, 'subcategory_id');
    }

    /**
     * Get all media files associated with the event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function media(): HasMany
    {
        return $this->hasMany(EventMedia::class, 'event_id');
    }

    /**
     * Get all users attending the event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_attendees');
    }

    /**
     * Check if a user is attending the event.
     *
     * @param int $userId
     * @return bool
     */
    public function isUserAttending($userId): bool
    {
        return $this->attendees()->where('user_id', $userId)->exists();
    }
}
