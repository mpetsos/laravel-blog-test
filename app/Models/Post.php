<?php

/*
 * Laravel Blog Test
 * by Thomas
 * Post Model
 */

namespace App\Models;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'slug',
        'category_id',
    ];

    // author user
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function booted()
    {
        // Delete comments on post deletion
        static::deleting(function ($post) {
            $post->comments()->delete();
        });
    }

    // Relationships
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
