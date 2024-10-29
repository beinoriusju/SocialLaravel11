<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;
use App\Models\Like;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Events\NotificationSent; // Import the event class

class LikePost extends Component
{
    public $post;
    public $currentReaction;
    public $totalLikes;
    public $currentReactionImage;

    protected $listeners = ['likesUpdated' => 'refreshLikes'];

    public function mount($post)
    {
        $this->post = $post;

        // Count total likes for the post (from the posts table)
        $this->totalLikes = $post->likes;

        // Get the current user's reaction if they have already reacted
        $like = Auth::user()->likes()->where('post_id', $post->id)->first();
        $this->currentReaction = $like ? $like->reaction_type : null;
        $this->currentReactionImage = $this->getReactionImage($this->currentReaction);
    }

    public function react($reactionType)
    {
        $like = Like::where('post_id', $this->post->id)
                    ->where('user_id', Auth::id())
                    ->first();

        if ($like) {
            // Toggle: if the same reaction is clicked, remove it (reaction removed)
            if ($like->reaction_type === $reactionType) {
                $this->handleReactionRemoval($like);
            } else {
                // Update to the new reaction (reaction edited)
                $this->handleReactionEdit($like, $reactionType);
            }
        } else {
            // Create new reaction (reaction added)
            $this->handleNewReaction($reactionType);
        }

        // Update the total count of likes for the post
        $this->totalLikes = $this->post->likes;
        $this->currentReactionImage = $this->getReactionImage($this->currentReaction);

        // Emit an event to refresh the likes for this specific post
        $this->dispatch('likesUpdated', $this->post->id);
    }

    private function handleNewReaction($reactionType)
    {
        Like::create([
            'post_id' => $this->post->id,
            'user_id' => Auth::id(),
            'reaction_type' => $reactionType,
        ]);

        $this->currentReaction = $reactionType;
        $this->post->increment('likes');

        // Notify the post owner if it's not the current user reacting to their own post
        if ($this->post->user_id != Auth::id()) {
            $notification = Notification::create([
                'type' => 'like',
                'receiver_id' => $this->post->user_id, // Post owner's user ID
                'sender_id' => Auth::id(), // Current user sending the notification
                'message' => "reacted to your post with a " . $reactionType . ".",
                'url' => ' ',
                'read_at' => null, // Unread by default
            ]);

            // Broadcast the notification
            broadcast(new NotificationSent($notification))->toOthers();
        }
    }

    private function handleReactionEdit($like, $reactionType)
    {
        $like->update(['reaction_type' => $reactionType]);
        $this->currentReaction = $reactionType;

        // Notify the post owner about reaction edit
        if ($this->post->user_id != Auth::id()) {
            $notification = Notification::create([
                'type' => 'edit_reaction',
                'receiver_id' => $this->post->user_id, // Post owner's user ID
                'sender_id' => Auth::id(), // Current user sending the notification
                'message' => "changed their reaction to " . $reactionType . " on your post.",
                'url' => ' ',
                'read_at' => null, // Unread by default
            ]);

            // Broadcast the notification
            broadcast(new NotificationSent($notification))->toOthers();
        }
    }

    private function handleReactionRemoval($like)
    {
        $like->delete();
        $this->currentReaction = null;

        // Ensure likes don't go below zero
        if ($this->post->likes > 0) {
            $this->post->decrement('likes');
        }

        // Notify the post owner about reaction removal
        if ($this->post->user_id != Auth::id()) {
            $notification = Notification::create([
                'type' => 'remove_reaction',
                'receiver_id' => $this->post->user_id, // Post owner's user ID
                'sender_id' => Auth::id(), // Current user sending the notification
                'message' => "removed their reaction from your post.",
                'url' => ' ',
                'read_at' => null, // Unread by default
            ]);

            // Broadcast the notification
            broadcast(new NotificationSent($notification))->toOthers();
        }
    }

    public function refreshLikes($postId)
    {
        if ($this->post->id == $postId) {
            // Refresh the post details after updating likes
            $this->post->refresh();
            $this->totalLikes = $this->post->likes;

            $like = Auth::user()->likes()->where('post_id', $this->post->id)->first();
            $this->currentReaction = $like ? $like->reaction_type : null;
            $this->currentReactionImage = $this->getReactionImage($this->currentReaction);
        }
    }

    private function getReactionImage($reactionType)
    {
        switch ($reactionType) {
            case 'like':
                return 'front/images/like.png';
            case 'love':
                return 'front/images/love.png';
            case 'happy':
                return 'front/images/happy.png';
            case 'haha':
                return 'front/images/haha.png';
            case 'think':
                return 'front/images/think.png';
            case 'sad':
                return 'front/images/sad.png';
            case 'lovely':
                return 'front/images/lovely.png';
            default:
                return 'front/images/likes.png'; // Default icon if no reaction
        }
    }

    public function render()
    {
        return view('livewire.like-post');
    }
}
