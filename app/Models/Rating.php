<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content_type',
        'content_id',
        'rating',
        'review',
        'contains_spoilers',
    ];

    protected $casts = [
        'rating' => 'float',
        'contains_spoilers' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function content(): MorphTo
    {
        return $this->morphTo();
    }
}