<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
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
    }
}