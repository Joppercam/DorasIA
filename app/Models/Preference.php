<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Preference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'favorite_genres',
        'favorite_countries',
        'email_notifications',
        'dark_mode',
        'content_language',
    ];

    protected $casts = [
        'favorite_genres' => 'array',
        'favorite_countries' => 'array',
        'email_notifications' => 'boolean',
        'dark_mode' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}