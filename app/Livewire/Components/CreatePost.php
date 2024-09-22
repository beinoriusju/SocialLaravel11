<?php

namespace App\Livewire\Components;

use App\Models\Post;
use App\Models\PostMedia;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class CreatePost extends Component
{
    use WithFileUploads;

    public $content;
    public $images = [];
    public $video;
    public $is_public = 1; // Default to "Public" (1)
    public $currentForm = 'post'; // Manage form visibility

    public function switchForm($form)
    {
        $this->currentForm = $form; // Switch between 'post' and 'blog'
    }

    public function render()
    {
      return view('livewire.components.create-post');
    }

    public function createpost()
    {
        // Validate that at least one of the fields is required and ensure file size limits
        $this->validate([
            'content' => 'nullable|string',
            'images.*' => 'nullable|image|max:200000',
            'video.*' => 'nullable|mimes:mp4,avi,mkv|max:200000', // Allow multiple videos
            'is_public' => 'required|boolean', // 1 for Public, 0 for Friends
        ]);

        // Ensure that at least one of content, images, or video is provided
        if (!$this->content && !$this->images && !$this->video) {
          $this->addError('content', __('translations.At least one of content, images, or video must be provided.'));
            return;
        }

        // Extract YouTube links from the content (if any)
        $youtubeLinks = $this->extractYouTubeLinks($this->content);

        DB::beginTransaction();
        try {
            // Creating post
            $post = Post::create([
                'uuid' => Str::uuid(),
                'user_id' => auth()->id(),
                'content' => $this->content ?? '', // Default to empty string if content is null
                'is_public' => $this->is_public, // Set the is_public field
            ]);

            // Save YouTube links (if any)
            if (!empty($youtubeLinks)) {
                foreach ($youtubeLinks as $link) {
                    PostMedia::create([
                        'post_id' => $post->id,
                        'file_type' => 'youtube', // Store YouTube links as 'youtube'
                        'file' => $link,
                        'position' => 'general',
                    ]);
                }
            }

            // Process images and save to user-specific folder
            if ($this->images) {
                $images = [];
                foreach ($this->images as $image) {
                    $images[] = $image->store("posts/{$post->user_id}/images", 'public');
                }
                PostMedia::create([
                    'post_id' => $post->id,
                    'file_type' => 'image',
                    'file' => json_encode($images),
                    'position' => 'general',
                ]);
            }

            // Process video and save to user-specific folder
            if ($this->video) {
                $videos = [];
                foreach ($this->video as $video) {
                    $videos[] = $video->store("posts/{$post->user_id}/videos", 'public');
                }
                PostMedia::create([
                    'post_id' => $post->id,
                    'file_type' => 'video',
                    'file' => json_encode($videos), // Store all videos as JSON
                    'position' => 'general',
                ]);
            }


            DB::commit();

            // Emit an event to trigger a page refresh
            // $this->reset();
            $this->dispatch('reload');

            // $this->render();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        // Clear fields
        $this->reset(['content', 'images', 'video', 'is_public']);
        $this->dispatch('reload');

    }

    /**
     * Extract all YouTube video and playlist links from content.
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
}
