<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'tv_show_id',
        'season_id',
        'episode_number',
        'name',
        'overview',
        'still_path',
        'runtime',
        'air_date',
        'api_id',
    ];

    protected $casts = [
        'air_date' => 'date',
        'episode_number' => 'integer',
        'runtime' => 'integer',
    ];

    public function tvShow(): BelongsTo
    {
        return $this->belongsTo(TvShow::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }
}