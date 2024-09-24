<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;
use App\Models\PostMedia;

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

            // Extract new YouTube links from the updated content
            $newYouTubeLinks = $this->extractYouTubeLinks($this->content);

            // Remove all existing YouTube links for the post
            PostMedia::where('post_id', $this->postId)
                ->where('file_type', 'youtube')
                ->delete();

            // Add the new YouTube links
            foreach ($newYouTubeLinks as $link) {
                PostMedia::create([
                    'post_id' => $this->postId,
                    'file_type' => 'youtube',
                    'file' => $link,
                    'position' => 'general',
                ]);
            }

            // Emit the event to the Newsfeed component to trigger post refresh
            $this->dispatch('postUpdated', $this->postId);
        }
    }

    /**
     * Extract YouTube links from the content.
     *
     * @param string|null $content
     * @return array
     */
     protected function extractYouTubeLinks($content)
     {
         if (!$content) {
             return [];
         }

         // Regular expressions to match YouTube video and playlist URLs
         $videoPattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w\-]+)/i';
         $playlistPattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/playlist\?list=)([\w\-]+)/i';

         $matches = [];
         preg_match_all($videoPattern, $content, $videoMatches);
         preg_match_all($playlistPattern, $content, $playlistMatches);

         // Combine video and playlist matches
         $matches = array_merge($videoMatches[0], $playlistMatches[0]);

         return $matches;
     }

    public function render()
    {
        return view('livewire.edit-post')->extends('layouts.app');
    }
}
