<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'profile_id',
    ];

    /**
     * Get the comment that owns the like.
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Get the profile that created the like.
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}