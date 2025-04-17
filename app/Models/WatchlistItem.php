<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WatchlistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'watchlist_id',
        'content_type',
        'content_id',
        'position',
        'note',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    public function watchlist(): BelongsTo
    {
        return $this->belongsTo(Watchlist::class);
    }

    public function content(): MorphTo
    {
        return $this->morphTo();
    }
}