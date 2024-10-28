<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSubCategory extends Model
{
    use HasFactory;

    // Explicitly define the table name
    protected $table = 'event_subcategories';

    // Define the relationship to EventCategory
    public function category()
    {
        return $this->belongsTo(EventCategory::class);
    }
}
