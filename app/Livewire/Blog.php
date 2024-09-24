<?php

namespace App\Livewire;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogSubCategory;
use Livewire\Component;
use Livewire\WithFileUploads;

class Blog extends Component
{
    use WithFileUploads;

    public $categories = [];
    public $subcategories = [];
    public $selectedCategory = null;
    public $selectedSubcategory = null;
    public $title;
    public $description;
    public $details;
    public $images = [];
    public $video;
    public $editMode = false;
    public $postIdBeingEdited = null;

    // Infinite scroll variables
    public $postsPerPage = 10;
    public $page = 1;
    public $hasMorePages = true;
    public $loadedPosts = [];

    protected $listeners = [
        'refresh' => '$refresh',
    ];

    public function mount()
    {
        // Fetch all categories with subcategories
        $this->categories = BlogCategory::with('blogSubCategories')->get();
        $this->loadMorePosts(); // Initial load of posts
    }

    public function updatedSelectedCategory($categoryId)
    {
        if ($categoryId) {
            $this->subcategories = BlogSubCategory::where('category_id', $categoryId)->get();
        } else {
            $this->subcategories = [];
        }

        $this->selectedSubcategory = null;
        $this->resetPosts();
    }

    public function updatedSelectedSubcategory($subcategoryId)
    {
        $this->resetPosts();
    }

    public function resetPosts()
    {
        // Reset pagination and post loading
        $this->page = 1;
        $this->hasMorePages = true;
        $this->loadedPosts = [];
        $this->loadMorePosts();
    }

    public function createBlogPost()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'details' => 'nullable|string',
            'images.*' => 'nullable|image|max:51200',
            'video' => 'nullable|mimes:mp4,mkv|max:51200',
        ]);

        BlogPost::create([
            'title' => $this->title,
            'description' => $this->description,
            'details' => $this->details,
            'category_id' => $this->selectedCategory,
            'subcategory_id' => $this->selectedSubcategory,
            'user_id' => auth()->id(),
        ]);

        // Reset form after submission
        $this->resetForm();
    }

    public function editPost($postId)
    {
        $this->editMode = true;
        $this->postIdBeingEdited = $postId;

        // Find the post and load its details
        $post = BlogPost::findOrFail($postId);

        // Load post data into the form fields
        $this->title = $post->title;
        $this->description = $post->description;
        $this->details = $post->details;
        $this->selectedCategory = $post->category_id;
        $this->selectedSubcategory = $post->subcategory_id;

        // Fetch subcategories for the selected category
        if ($this->selectedCategory) {
            $this->subcategories = BlogSubCategory::where('category_id', $this->selectedCategory)->get();
        }
    }

    public function updateBlogPost()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'details' => 'nullable|string',
            'images.*' => 'nullable|image|max:51200',
            'video' => 'nullable|mimes:mp4,mkv|max:51200',
        ]);

        $post = BlogPost::findOrFail($this->postIdBeingEdited);
        $post->update([
            'title' => $this->title,
            'description' => $this->description,
            'details' => $this->details,
            'category_id' => $this->selectedCategory,
            'subcategory_id' => $this->selectedSubcategory,
        ]);

        $this->resetForm();
        $this->resetPosts();
    }

    public function deletePost($postId)
    {
        $post = BlogPost::findOrFail($postId);

        // Get associated media
        $mediaItems = $post->media;

        // Loop through each media item and delete it from storage
        foreach ($mediaItems as $media) {
            if ($media->file_type == 'image' || $media->file_type == 'video') {
                // Delete the file from storage
                \Storage::disk('public')->delete($media->file);
            }

            // Delete the media record from the database
            $media->delete();
        }

        // Delete the post itself
        $post->delete();

        // Refresh the component to update the UI
        $this->resetPosts();
    }

    public function resetForm()
    {
        $this->editMode = false;
        $this->title = '';
        $this->description = '';
        $this->details = '';
        $this->images = [];
        $this->video = '';
        $this->selectedCategory = null;
        $this->selectedSubcategory = null;
        $this->postIdBeingEdited = null;
    }

    // Method to load more posts for infinite scrolling
    public function loadMorePosts()
    {
        if (!$this->hasMorePages) {
            return;
        }

        $query = BlogPost::with('media');

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        if ($this->selectedSubcategory) {
            $query->where('subcategory_id', $this->selectedSubcategory);
        }

        $newPosts = $query->latest()->skip(($this->page - 1) * $this->postsPerPage)->take($this->postsPerPage)->get();

        if ($newPosts->isEmpty()) {
            $this->hasMorePages = false;
        } else {
            // Add new posts to the loaded posts
            $this->loadedPosts = array_merge($this->loadedPosts, $newPosts->toArray());

            // Check if there are more posts to load
            if ($newPosts->count() < $this->postsPerPage) {
                $this->hasMorePages = false;
            } else {
                $this->page++;
            }
        }
    }

    public function render()
    {
        return view('livewire.blog', [
            'blogPosts' => $this->loadedPosts,
            'hasMorePages' => $this->hasMorePages,
        ])->extends("layouts.app");
    }
}
