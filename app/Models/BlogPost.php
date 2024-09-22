<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use HasFactory, SoftDeletes;

    // Guarded attributes for mass assignment
    protected $guarded = [];

    /**
     * Get the user that owns the blog post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the category that the blog post belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    /**
     * Get the subcategory that the blog post belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(BlogSubCategory::class, 'subcategory_id');
    }

    /**
     * Get all media files associated with the blog post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function media(): HasMany
    {
        return $this->hasMany(BlogPostMedia::class, 'blog_post_id');
    }

    // You can add any other custom methods or relationships needed
}
