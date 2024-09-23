<?php

namespace App\Livewire;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogSubCategory;
use Livewire\Component;
use Livewire\WithPagination;
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

    public $page = 1; // Current page for infinite scrolling
    public $hasMorePosts = true; // To track if there are more posts to load
    public $blogPosts = []; // To store loaded blog posts

    public function mount()
    {
        // Fetch all categories with subcategories
        $this->categories = BlogCategory::with('blogSubCategories')->get();
        $this->loadMorePosts(); // Load the initial posts
    }

    public function updatedSelectedCategory($categoryId)
    {
        if ($categoryId) {
            $this->subcategories = BlogSubCategory::where('category_id', $categoryId)->get();
        } else {
            $this->subcategories = [];
        }
        $this->selectedSubcategory = null;
        $this->resetPosts(); // Reset the post list after a filter is selected
    }

    public function updatedSelectedSubcategory()
    {
        $this->resetPosts(); // Reset the post list after a subcategory filter is selected
    }

    public function resetPosts()
    {
        $this->page = 1;
        $this->hasMorePosts = true;
        $this->blogPosts = [];
        $this->loadMorePosts(); // Reload posts after resetting
    }

    public function loadMorePosts()
    {
        if ($this->hasMorePosts) {
            $newPosts = BlogPost::with('media')
                ->when($this->selectedCategory, function ($query) {
                    $query->where('category_id', $this->selectedCategory);
                })
                ->when($this->selectedSubcategory, function ($query) {
                    $query->where('subcategory_id', $this->selectedSubcategory);
                })
                ->latest()
                ->paginate(10, ['*'], 'page', $this->page);

            if ($newPosts->isNotEmpty()) {
                $this->blogPosts = array_merge($this->blogPosts, $newPosts->items());
                $this->page++;
                $this->hasMorePosts = $newPosts->hasMorePages();
            } else {
                $this->hasMorePosts = false;
            }
        }
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
        $post = BlogPost::findOrFail($postId);

        $this->title = $post->title;
        $this->description = $post->description;
        $this->details = $post->details;
        $this->selectedCategory = $post->category_id;
        $this->selectedSubcategory = $post->subcategory_id;
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

    public function render()
    {
        return view('livewire.blog', [
            'blogPosts' => $this->blogPosts,
        ])->extends("layouts.app");
    }
}
