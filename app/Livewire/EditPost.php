<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;

class EditPost extends Component
{
    public $postId;
    public $content;

    protected $rules = [
        'content' => 'required|string',
    ];

    public function mount($postId)
    {
        $post = Post::find($postId);
        if ($post) {
            $this->postId = $postId;
            $this->content = $post->content;
        }
    }

    public function updatePost()
    {
        $this->validate();

        $post = Post::find($this->postId);
        if ($post) {
            $post->content = $this->content;
            $post->save();

            // Emit the event to the Newsfeed component to trigger post refresh
            $this->dispatch('postUpdated', $this->postId);
        }
    }

    public function render()
    {
        return view('livewire.edit-post')->extends('layouts.app');
    }
}
