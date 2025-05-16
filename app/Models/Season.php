<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    use HasFactory;

    protected $fillable = [
<<<<<<< HEAD
        'title_id',
        'name',
        'number',
        'overview',
        'poster',
        'air_date',
    ];

    protected $casts = [
        'number' => 'integer',
        'air_date' => 'date',
    ];

    /**
     * Get the title that this season belongs to.
     */
    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class);
    }

    /**
     * Get the episodes in this season.
     */
    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class)->orderBy('number');
=======
        'tv_show_id',
        'season_number',
        'name',
        'overview',
        'poster_path',
        'air_date',
        'episode_count',
        'api_id',
    ];

    protected $casts = [
        'air_date' => 'date',
        'season_number' => 'integer',
        'episode_count' => 'integer',
    ];

    public function tvShow(): BelongsTo
    {
        return $this->belongsTo(TvShow::class);
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class)->orderBy('episode_number');
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
    }
}