<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class CommentLiked extends Notification implements ShouldQueue
{
    use Queueable;

    protected $comment;
    protected $liker;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $comment, Profile $liker)
    {
        $this->comment = $comment;
        $this->liker = $liker;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'liker_id' => $this->liker->id,
            'liker_name' => $this->liker->user->name,
            'liker_avatar' => $this->liker->avatar,
            'title_slug' => $this->comment->title->slug,
            'title_name' => $this->comment->title->name,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'comment_id' => $this->comment->id,
            'liker_name' => $this->liker->user->name,
            'liker_avatar' => $this->liker->avatar,
            'message' => 'A ' . $this->liker->user->name . ' le gustó tu comentario',
            'url' => route('titles.show', $this->comment->title->slug) . '#comment-' . $this->comment->id,
        ]);
    }

    /**
     * Get the type of the notification being broadcast.
     */
    public function broadcastType(): string
    {
        return 'comment-liked';
    }
}