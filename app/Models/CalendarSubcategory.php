<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarSubCategory extends Model
{
    use HasFactory;

    // Explicitly define the table name
    protected $table = 'calendar_subcategories';

    // Define the relationship to CalendarCategory
    public function category()
    {
        return $this->belongsTo(CalendarCategory::class);
    }
}
