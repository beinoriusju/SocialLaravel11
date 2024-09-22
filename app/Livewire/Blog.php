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
    use WithPagination, WithFileUploads;

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

    public function mount()
    {
        // Fetch all categories with subcategories
        $this->categories = BlogCategory::with('blogSubCategories')->get();
    }

    public function updatedSelectedCategory($categoryId)
    {
        if ($categoryId) {
            $this->subcategories = BlogSubCategory::where('category_id', $categoryId)->get();
        } else {
            $this->subcategories = [];
        }

        $this->selectedSubcategory = null;
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
        $query = BlogPost::with('media');

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        if ($this->selectedSubcategory) {
            $query->where('subcategory_id', $this->selectedSubcategory);
        }

        $blogPosts = $query->latest()->paginate(10);

        return view('livewire.blog', [
            'blogPosts' => $blogPosts,
        ])->extends("layouts.app");
    }
}
