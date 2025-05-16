<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\Relations\MorphTo;
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
<<<<<<< HEAD
        'profile_id',
        'title_id',
        'score',
        'review',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    /**
     * Get the profile that created the rating.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the title that was rated.
     */
    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class);
=======
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
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
    }
}