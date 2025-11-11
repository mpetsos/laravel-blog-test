<?php

/*
 * Laravel Blog Test
 * by Thomas
 * Notification to author  (via email) when added a comment
 */

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    use Queueable;

    protected $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Comment on Your Post')
            ->line("A new comment has been added to your post: '{$this->comment->post->title}'")
            ->line("Comment: {$this->comment->content}")
            ->action('View Post', url("/posts/{$this->comment->post->id}/{$this->comment->post->slug}"))
            ->line('Thank you for using our blog!');
    }
}
