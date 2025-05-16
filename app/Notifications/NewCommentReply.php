<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewCommentReply extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reply;
    protected $originalComment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $reply, Comment $originalComment)
    {
        $this->reply = $reply;
        $this->originalComment = $originalComment;
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
            'reply_id' => $this->reply->id,
            'comment_id' => $this->originalComment->id,
            'replier_id' => $this->reply->profile->id,
            'replier_name' => $this->reply->profile->user->name,
            'replier_avatar' => $this->reply->profile->avatar,
            'title_slug' => $this->originalComment->commentable->slug,
            'title_name' => $this->originalComment->commentable->name,
            'comment_preview' => substr($this->reply->content, 0, 50) . '...',
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'reply_id' => $this->reply->id,
            'comment_id' => $this->originalComment->id,
            'replier_name' => $this->reply->profile->user->name,
            'replier_avatar' => $this->reply->profile->avatar,
            'message' => $this->reply->profile->user->name . ' respondió a tu comentario',
            'url' => route('titles.show', $this->originalComment->commentable->slug) . '#comment-' . $this->originalComment->id,
        ]);
    }

    /**
     * Get the type of the notification being broadcast.
     */
    public function broadcastType(): string
    {
        return 'new-comment-reply';
    }
}