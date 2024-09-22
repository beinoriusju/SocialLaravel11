<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use HasFactory;

    // Define the relationship to BlogSubCategory
    public function blogSubCategories()
    {
        return $this->hasMany(BlogSubCategory::class, 'category_id');
    }
}
