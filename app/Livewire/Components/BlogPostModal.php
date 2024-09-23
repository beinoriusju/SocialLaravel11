<?php

namespace App\Livewire\Components;

use App\Models\BlogCategory;
use App\Models\BlogSubCategory;
use App\Models\BlogPost;
use App\Models\BlogPostMedia;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class BlogPostModal extends Component
{
    use WithFileUploads;

    public $title;
    public $description;
    public $details;
    public $images = [];
    public $video = []; // Allow multiple videos
    public $is_public = 1; // Default to "Public"
    public $categories = [];
    public $subcategories = [];
    public $blogCategory;
    public $blogSubCategory;

    public function mount()
    {
        // Fetch all blog categories with their subcategories
        $this->categories = BlogCategory::with('blogSubCategories')->get();
    }

    // Update subcategories when a category is selected
    public function updatedBlogCategory($categoryId)
    {
        if ($categoryId) {
            // Fetch subcategories related to the selected blog category
            $this->subcategories = BlogSubCategory::where('category_id', $categoryId)->get();

            // If no subcategories are found, reset the selected subcategory
            if ($this->subcategories->isEmpty()) {
                $this->blogSubCategory = null;
            }
        } else {
            // Clear subcategories and reset subcategory selection
            $this->subcategories = [];
            $this->blogSubCategory = null;
        }
    }

    public function createBlogPost()
    {
        // Validate the blog post inputs
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'details' => 'nullable|string',
            'blogCategory' => 'required|exists:blog_categories,id',
            'blogSubCategory' => 'nullable|exists:blog_subcategories,id',
            'images.*' => 'nullable|image|max:51200', // 50 MB in kilobytes
            'video.*' => 'nullable|mimes:mp4,avi,mkv|max:51200', // Allow multiple videos, 50 MB each
            'is_public' => 'required|boolean', // 1 for Public, 0 for Friends
        ]);

        // Extract YouTube links from the title, description, and details
        $youtubeLinks = $this->extractYouTubeLinks($this->title, $this->description, $this->details);

        DB::beginTransaction();
        try {
            // Create the blog post
            $blogPost = BlogPost::create([
                'uuid' => Str::uuid(),
                'user_id' => auth()->id(),
                'title' => $this->title,
                'description' => $this->description,
                'details' => $this->details,
                'category_id' => $this->blogCategory,
                'subcategory_id' => $this->blogSubCategory,
                'is_public' => $this->is_public,
            ]);

            // Save YouTube links (if any)
            if (!empty($youtubeLinks)) {
                foreach ($youtubeLinks as $link) {
                    BlogPostMedia::create([
                        'blog_post_id' => $blogPost->id, // Correct column name
                        'file_type' => 'youtube', // Store YouTube links as 'youtube'
                        'file' => $link,
                        'position' => 'general',
                    ]);
                }
            }

            // Process images and save to user-specific folder
            if ($this->images) {
                foreach ($this->images as $image) {
                    $imagePath = $image->store("blog_posts/{$blogPost->user_id}/images", 'public');
                    BlogPostMedia::create([
                        'blog_post_id' => $blogPost->id, // Correct column name
                        'file_type' => 'image',
                        'file' => $imagePath,
                        'position' => 'general',
                    ]);
                }
            }

            // Process videos and save to user-specific folder
            if ($this->video) {
                $videos = [];
                foreach ($this->video as $video) {
                    $videos[] = $video->store("blog_posts/{$blogPost->user_id}/videos", 'public');
                }
                foreach ($videos as $videoFilePath) {
                    BlogPostMedia::create([
                        'blog_post_id' => $blogPost->id, // Correct column name
                        'file_type' => 'video',
                        'file' => $videoFilePath,
                        'position' => 'general',
                    ]);
                }
            }

            DB::commit();

            // Emit an event to refresh the list of blog posts
            $this->dispatch('reload');

            // Dispatch a browser event to notify the user about the successful post creation
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Blog post created successfully!'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        // Clear the form fields
        $this->reset(['title', 'description', 'details', 'images', 'video', 'blogCategory', 'blogSubCategory', 'is_public']);
    }

    /**
     * Extract all YouTube video and playlist links from the title, description, and details.
     *
     * @param string|null $title
     * @param string|null $description
     * @param string|null $details
     * @return array
     */
    protected function extractYouTubeLinks($title, $description, $details)
    {
        // Combine all fields into one string
        $content = $title . ' ' . $description . ' ' . $details;

        if (!$content) {
            return [];
        }

        // Regular expressions to match YouTube video and playlist URLs
        $videoPattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w\-]+)/i';
        $playlistPattern = '/(?:https?:\/\/)?(?:youtube\.com\/playlist\?list=)([\w\-]+)/i';

        $matches = [];
        preg_match_all($videoPattern, $content, $videoMatches);
        preg_match_all($playlistPattern, $content, $playlistMatches);

        // Combine video and playlist matches
        $matches = array_merge($videoMatches[0], $playlistMatches[0]);

        return $matches;
    }

    public function render()
    {
        return view('livewire.components.blog-post-modal');
    }
}
