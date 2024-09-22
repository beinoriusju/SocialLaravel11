<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Post;
use App\Models\Like;
use App\Models\PostMedia;
use App\Models\Comment;
use App\Models\User;
use App\Models\Friend;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserProfile extends Component
{
    use WithFileUploads, WithPagination;

    public $editingPostId = null;
    public $reactionCounts = [];
    public $likes = [];
    public $comment;
    public $editingCommentId = null;
    public $editedComment;
    public $user;
    public $newProfileImage;

    // Fields for About Me section
    public $about_me;
    public $birthday;
    public $hobbies;

    // Field for friends list
    public $friends;

    // Fields for photos and infinite scrolling
    public $profilePhotos = [];
    public $postPhotos = [];
    public $profilePhotosPage = 1;
    public $postPhotosPage = 1;
    public $profilePhotosLoaded = true;
    public $postPhotosLoaded = true;

    // Properties for posts loading and infinite scroll
    public $posts = [];
    public $postsPage = 1;
    public $postsLoaded = true;

    protected $listeners = [
        'postCreated' => '$refresh',
        'postUpdated' => 'closeEditForm',
        'likesUpdated' => '$refresh',
    ];

    public function mount(User $user)
    {
        $this->user = $user;

        // Set default values for about me section fields
        $this->about_me = $this->user->description;
        $this->birthday = $this->user->birthday;
        $this->hobbies = $this->user->hobbies;

        // Fetch friends and media (photos)
        $this->fetchFriends();
        $this->loadProfilePhotos();
        $this->loadPostPhotos();
        $this->loadMorePosts(); // Load initial posts
    }

    public function loadMorePosts()
    {
        if ($this->postsLoaded) {
            $authUserId = auth()->id();
            $profileUserId = $this->user->id;

            $newPosts = Post::with('user', 'likes', 'comments')
                ->where('user_id', $profileUserId)
                ->where(function ($query) use ($authUserId, $profileUserId) {
                    $query->where('is_public', 0) // Public posts
                          ->orWhere('user_id', $authUserId) // Authenticated user's own posts
                          ->orWhere(function ($query) use ($authUserId) {
                              // Friends-only posts if the authenticated user is a friend
                              $query->where('is_public', 1)
                                    ->whereHas('user.sentFriendRequests', function ($friendQuery) use ($authUserId) {
                                        $friendQuery->where('friend_id', $authUserId)
                                                    ->where('status', 'accepted');
                                    })
                                    ->orWhereHas('user.receivedFriendRequests', function ($friendQuery) use ($authUserId) {
                                        $friendQuery->where('user_id', $authUserId)
                                                    ->where('status', 'accepted');
                                    });
                          });
                })
                ->latest()
                ->paginate(10, ['*'], 'page', $this->postsPage);

            if ($newPosts->isNotEmpty()) {
                $this->posts = array_merge($this->posts, $newPosts->items());
                // Increment the page only after successfully fetching posts
                $this->postsPage++;
            } else {
                $this->postsLoaded = false;
            }
        }
    }

    public function fetchFriends()
    {
        $this->friends = Friend::where(function ($query) {
            $query->where('user_id', $this->user->id)
                  ->orWhere('friend_id', $this->user->id);
        })
        ->where('status', 'accepted')
        ->get();
    }

    public function updatedNewProfileImage()
    {
        $this->validate([
            'newProfileImage' => 'image|max:200048', // 2MB Max
        ]);

        $userFolder = 'profileImages/' . $this->user->id;
        $imagePath = $this->newProfileImage->store($userFolder, 'public');

        $this->user->update([
            'image' => $imagePath,
        ]);

        $this->user->refresh();
    }

    public function updateAboutSection()
    {
        if (Auth::id() !== $this->user->id) {
            abort(403, 'Unauthorized action.');
        }

        $this->validate([
            'about_me' => 'nullable|string|max:1000',
            'birthday' => 'nullable|date',
            'hobbies' => 'nullable|string|max:1000',
        ]);

        $this->user->update([
            'description' => $this->about_me,
            'birthday' => $this->birthday,
            'hobbies' => $this->hobbies,
        ]);

        session()->flash('message', 'Profile updated successfully!');
    }

    public $activeTab = 'timeline';

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public $activePhotoTab = 'profilePhotos';

    public function setActivePhotoTab($tab)
    {
        $this->activePhotoTab = $tab;
    }

    public function saveComment($post_id)
    {
        $this->validate([
            "comment" => "required|string"
        ]);

        DB::beginTransaction();
        try {
            Comment::firstOrCreate([
                "post_id" => $post_id,
                "comment" => $this->comment,
                "user_id" => auth()->id()
            ]);
            $post = Post::findOrFail($post_id);
            $post->comments += 1;
            $post->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        unset($this->comment);
    }

    // Edit a comment
    public function editComment($commentId)
    {
        $comment = Comment::find($commentId);
        if ($comment && $comment->user_id == auth()->id()) {
            $this->editingCommentId = $commentId;
            $this->editedComment = $comment->comment;
        }
    }

    // Update a comment
    public function updateComment($commentId)
    {
        $this->validate([
            'editedComment' => 'required|string',
        ]);

        $comment = Comment::find($commentId);
        if ($comment && $comment->user_id == auth()->id()) {
            DB::beginTransaction();
            try {
                $comment->update([
                    'comment' => $this->editedComment,
                ]);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
            }

            $this->editingCommentId = null;
            $this->editedComment = null;
        }
    }

    public function react($id, $reactionType)
    {
        DB::beginTransaction();
        try {
            $like = Like::where(["post_id" => $id, "user_id" => auth()->id()])->first();

            if ($like) {
                if ($like->reaction_type === $reactionType) {
                    $like->delete();
                } else {
                    $like->update(['reaction_type' => $reactionType]);
                }
            } else {
                Like::create([
                    'post_id' => $id,
                    'user_id' => auth()->id(),
                    'reaction_type' => $reactionType
                ]);
            }

            $this->updateReactionCounts($id);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    // Update reaction counts and include user details for rendering
    public function updateReactionCounts($postId)
    {
        $likes = Like::with('user')->where('post_id', $postId)->get()->groupBy('reaction_type');

        $reactionTypes = ['like', 'love', 'happy', 'haha', 'think', 'sad', 'lovely'];

        foreach ($reactionTypes as $reactionType) {
            $this->reactionCounts[$postId][$reactionType] = isset($likes[$reactionType]) ? $likes[$reactionType]->count() : 0;
        }

        $this->likes[$postId] = $likes->map(function ($group) {
            return $group->map(function ($like) {
                if ($like->user) {
                    return [
                        'user_id' => $like->user->id ?? null,
                        'user' => $like->user->name ?? 'Unknown User',
                        'user_image' => $like->user->image ?? 'default.png',
                        'reaction_type' => $like->reaction_type,
                    ];
                } else {
                    return [
                        'user_id' => null,
                        'user' => 'Unknown User',
                        'user_image' => 'default.png',
                        'reaction_type' => $like->reaction_type,
                    ];
                }
            });
        })->toArray();
    }
    // Delete a comment
    public function deleteComment($commentId)
    {
        $comment = Comment::find($commentId);

        if ($comment && ($comment->user_id == auth()->id() || $comment->post->user_id == auth()->id())) {
            DB::beginTransaction();
            try {
                $post = Post::findOrFail($comment->post_id);
                if ($post->comments > 0) {
                    $post->comments -= 1;
                    $post->save();
                }

                $comment->delete();
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
            }
        }
    }

    public function deletePost($postId)
    {
        $post = Post::find($postId);
        if ($post && $post->user_id == auth()->id()) {
            $postMedia = PostMedia::where('post_id', $postId)->get();
            foreach ($postMedia as $media) {
                if ($media->file_type === 'image') {
                    $files = json_decode($media->file, true);
                    if (is_array($files)) {
                        foreach ($files as $file) {
                            Storage::disk('public')->delete($file);
                        }
                    } else {
                        Storage::disk('public')->delete($media->file);
                    }
                }
                $media->delete();
            }
            $post->delete();
        }
    }

    public function loadProfilePhotos()
    {
        $directory = "public/profileImages/{$this->user->id}/";
        if (Storage::exists($directory)) {
            $files = Storage::files($directory);

            $cleanedFiles = array_map(function ($file) {
                return str_replace('public/', '', $file);
            }, $files);

            $this->profilePhotos = array_merge($this->profilePhotos, array_slice($cleanedFiles, 10 * ($this->profilePhotosPage - 1), 10));
            $this->profilePhotosPage++;

            if (count($files) <= 10 * $this->profilePhotosPage) {
                $this->profilePhotosLoaded = false;
            }
        } else {
            $this->profilePhotos = [];
        }
    }

    public function loadPostPhotos()
    {
        $directory = "public/posts/{$this->user->id}/images/";
        if (Storage::exists($directory)) {
            $files = Storage::files($directory);

            $cleanedFiles = array_map(function ($file) {
                return str_replace('public/', '', $file);
            }, $files);

            $this->postPhotos = array_merge($this->postPhotos, array_slice($cleanedFiles, 10 * ($this->postPhotosPage - 1), 10));
            $this->postPhotosPage++;

            if (count($files) <= 10 * $this->postPhotosPage) {
                $this->postPhotosLoaded = false;
            }
        } else {
            $this->postPhotos = [];
        }
    }

    public function loadMoreProfilePhotos()
    {
        $this->profilePhotosPage++;
        $this->loadProfilePhotos();
    }

    public function loadMorePostPhotos()
    {
        $this->postPhotosPage++;
        $this->loadPostPhotos();
    }

    public function closeEditForm($postId)
    {
        $this->editingPostId = null;
        $this->dispatch('refresh');
    }

    public function startEditing($postId)
    {
        $this->editingPostId = $postId;
    }

    public function render()
    {
        $authUserId = auth()->id();
        $profileUserId = $this->user->id;

        // Check if the authenticated user is the profile owner
        $isOwner = $authUserId === $profileUserId;

        // Check if the authenticated user is a friend of the profile owner
        $isFriend = Friend::where(function ($query) use ($authUserId, $profileUserId) {
            $query->where('user_id', $authUserId)->where('friend_id', $profileUserId)
                  ->orWhere('user_id', $profileUserId)->where('friend_id', $authUserId);
        })->where('status', 'accepted')->exists();

        // Only fetch more posts if the posts array is empty (initial load)
        if (empty($this->posts)) {
            // Ensure posts are filtered based on privacy (public, friends-only, or own posts)
            $postsQuery = Post::with('user', 'likes', 'comments')
                ->where('user_id', $profileUserId)
                ->where(function ($query) use ($authUserId, $isOwner, $isFriend) {
                    $query->where('is_public', 0) // Public posts
                        ->orWhere('user_id', $authUserId) // User's own posts
                        ->orWhere(function ($query) use ($authUserId, $isFriend) {
                            // Friends-only posts if the authenticated user is a friend
                            if ($isFriend) {
                                $query->where('is_public', 1);
                            }
                        })
                        ->orWhere(function ($query) use ($isOwner) {
                            // Always show posts if the authenticated user is the profile owner
                            if ($isOwner) {
                                $query->whereNotNull('id'); // Dummy condition to include all posts for the owner
                            }
                        });
                })
                ->latest()
                ->paginate(10, ['*'], 'page', $this->postsPage);

            // Append posts to the $this->posts array
            $this->posts = $postsQuery->items();
        }

        // Ensure reactions are updated for all loaded posts
        foreach ($this->posts as $post) {
            $this->updateReactionCounts($post->id);
        }

        return view('livewire.user-profile', [
            'posts' => $this->posts,
            'reactionCounts' => $this->reactionCounts,
            'likes' => $this->likes,
            'friends' => $this->friends,
            'profilePhotos' => $this->profilePhotos,
            'postPhotos' => $this->postPhotos,
        ])->extends('layouts.app');
    }
}