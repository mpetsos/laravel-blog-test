<?php

/*
 * Laravel Blog Test
 * by Thomas
 * Comment model
 */

namespace App\Models;

use App\Notifications\NewCommentNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Import the models used in relationships
use App\Models\Post;
use App\Models\User;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'user_id', 'post_id'];

    protected static function booted()
    {
        // Notify post author when a new comment is created
        static::created(function ($comment) {
            $postAuthor = $comment->post->user;
            if ($postAuthor) {
                $postAuthor->notify(new NewCommentNotification($comment));
            }
        });
    }

    // Relationships

    /**
     * Get the post that the comment belongs to.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the user that wrote the comment (the author).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the author of the comment.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
