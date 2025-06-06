<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TitleRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'series_id', 
        'rating_type',
        'rating_value'
    ];

    protected $casts = [
        'rating_value' => 'integer'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    // Scopes
    public function scopeDislike($query)
    {
        return $query->where('rating_type', 'dislike');
    }

    public function scopeLike($query)
    {
        return $query->where('rating_type', 'like');
    }

    public function scopeLove($query)
    {
        return $query->where('rating_type', 'love');
    }

    // Helper methods
    public static function getRatingValue($type)
    {
        return match($type) {
            'dislike' => 1,
            'like' => 3,
            'love' => 5,
            default => 3
        };
    }

    public static function getRatingEmoji($type)
    {
        return match($type) {
            'dislike' => '👎',
            'like' => '👍',
            'love' => '❤️',
            default => '👍'
        };
    }
}