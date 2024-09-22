<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPostMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_post_id',
        'file_type',
        'file',
        'position',
    ];

    /**
     * Get the blog post that owns the media.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function blogPost(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }
}
