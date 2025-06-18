<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActorContentLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'actor_content_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function content()
    {
        return $this->belongsTo(ActorContent::class, 'actor_content_id');
    }
}