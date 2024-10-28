<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogSubCategory extends Model
{
    use HasFactory;

    // Explicitly define the table name
    protected $table = 'blog_subcategories';

    // Define the relationship to BlogCategory
    public function category()
    {
        return $this->belongsTo(BlogCategory::class);
    }
}
