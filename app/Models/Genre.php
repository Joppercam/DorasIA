<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_es',
        'tmdb_id'
    ];

    public function series(): BelongsToMany
    {
        return $this->belongsToMany(Series::class, 'series_genre');
    }

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movie_genre');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name_es ?: $this->name;
    }
}
