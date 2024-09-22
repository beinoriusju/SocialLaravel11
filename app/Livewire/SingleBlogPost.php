<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BlogPost;

class SingleBlogPost extends Component
{
    public $post;

    public function mount($post)
    {
        // Retrieve the post by its ID or slug (adjust based on your model setup)
        $this->post = BlogPost::with('media')->findOrFail($post);
    }

    public function render()
    {
        return view('livewire.single-blog-post', [
            'post' => $this->post,
        ])->extends('layouts.app');
    }
}
