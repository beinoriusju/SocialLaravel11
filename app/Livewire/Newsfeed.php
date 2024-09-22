<?php

namespace App\Livewire;
use Livewire\Component;
use App\Models\Post;
use App\Models\Like;
use App\Models\PostMedia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Comment;
use Livewire\WithPagination;

class Newsfeed extends Component
{
    use WithPagination;

    public $editingPostId = null;
    public $reactionCounts = [];
    public $likes = [];
    public $comments = [];
    public $editingCommentId = null;
    public $editedComment;
    public $postsPage = 1;
    public $postsLoaded = true;
    public $posts = []; // Initialized as an array

    protected $listeners = [
        'postCreated' => '$refresh',
        "deletePost" => 'deletePost',
        "post-updated" => '$refresh',
        'postUpdated' => 'closeEditForm',
        'postCreated' => 'addNewPost',
        'likesUpdated' => '$refresh',
        'refresh' => '$refresh',
    ];

    // Infinite scroll method to load more posts
    public function loadMorePostsNewsfeed()
    {
        if ($this->postsLoaded) {
            $this->postsPage++;

            $newPosts = Post::with('user', 'likes', 'comments')
                ->where(function ($query) {
                    $authUserId = auth()->id();
                    $query->where('is_public', 0)
                        ->orWhere('user_id', $authUserId)
                        ->orWhere(function ($query) use ($authUserId) {
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
                // Notify the frontend that posts have been loaded
                $this->dispatch('postsLoaded');
            } else {
                $this->postsLoaded = false; // No more posts to load
            }
        }
    }

    public function saveComment($post_id)
    {
        $this->validate([
            "comments.$post_id" => "required|string"
        ]);

        DB::beginTransaction();
        try {
            Comment::firstOrCreate([
                "post_id" => $post_id,
                "comment" => $this->comments[$post_id],
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
        unset($this->comments[$post_id]);
    }

    public function editComment($commentId)
    {
        $comment = Comment::find($commentId);
        if ($comment && $comment->user_id == auth()->id()) {
            $this->editingCommentId = $commentId;
            $this->editedComment = $comment->comment;
        }
    }

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

    public function updateReactionCounts($postId)
    {
        $likes = Like::with('user')->where('post_id', $postId)->get()->groupBy('reaction_type');

        $reactionTypes = ['like', 'love', 'happy', 'haha', 'think', 'sad', 'lovely'];
        foreach ($reactionTypes as $reactionType) {
            $this->reactionCounts[$postId][$reactionType] = isset($likes[$reactionType]) ? $likes[$reactionType]->count() : 0;
        }

        $this->likes[$postId] = $likes->map(function ($group) {
            return $group->map(function ($like) {
                return [
                    'user_id' => $like->user->id ?? null,
                    'user' => $like->user->name ?? 'Unknown User',
                    'user_image' => $like->user->image ?? 'default.png',
                    'reaction_type' => $like->reaction_type,
                ];
            });
        })->toArray();
    }

    public function deletePost($postId)
    {
        $post = Post::find($postId);
        if ($post) {
            $postMedia = PostMedia::where('post_id', $postId)->get();
            foreach ($postMedia as $media) {
                if ($media->file_type === 'image' || $media->file_type === 'video') {
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
            $this->dispatch('reload');
        }
    }

    public function closeEditForm($postId)
    {
        $this->editingPostId = null;
        $this->dispatch('$refresh');
    }

    public function startEditing($postId)
    {
        $this->editingPostId = $postId;
    }

    public function addNewPost()
    {
        $this->dispatch("refresh");
    }

    public function render()
    {
        $authUserId = auth()->id();

        // Load posts for the first page
        if ($this->postsPage === 1 && empty($this->posts)) {
            $newPosts = Post::with('user', 'likes', 'comments')
                ->where(function ($query) use ($authUserId) {
                    $query->where('is_public', 0)
                          ->orWhere('user_id', $authUserId)
                          ->orWhere(function ($query) use ($authUserId) {
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

            $this->posts = $newPosts->items();
        }

        foreach ($this->posts as $post) {
            $this->updateReactionCounts($post->id);
        }

        return view('livewire.newsfeed', [
            'posts' => $this->posts,
            'reactionCounts' => $this->reactionCounts,
            'likes' => $this->likes,
        ])->extends('layouts.app');
    }
}
