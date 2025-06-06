<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Series extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_es',
        'original_title',
        'overview',
        'overview_es',
        'synopsis',
        'synopsis_es',
        'status',
        'first_air_date',
        'last_air_date',
        'number_of_seasons',
        'number_of_episodes',
        'episode_run_time',
        'original_language',
        'origin_country',
        'vote_average',
        'vote_count',
        'popularity',
        'poster_path',
        'backdrop_path',
        'homepage',
        'in_production',
        'production_companies',
        'production_countries',
        'spoken_languages',
        'networks',
        'tagline',
        'tagline_es',
        'type',
        'tmdb_id',
        'imdb_id',
        'is_korean_drama'
    ];

    protected $casts = [
        'first_air_date' => 'date',
        'last_air_date' => 'date',
        'vote_average' => 'decimal:1',
        'popularity' => 'decimal:3',
        'in_production' => 'boolean',
        'is_korean_drama' => 'boolean',
        'production_companies' => 'array',
        'production_countries' => 'array',
        'spoken_languages' => 'array',
        'networks' => 'array'
    ];

    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'series_genre');
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'series_person')
                    ->withPivot(['role', 'character', 'order', 'department', 'job'])
                    ->withTimestamps();
    }

    public function actors(): BelongsToMany
    {
        return $this->people()->wherePivot('role', 'actor');
    }

    public function directors(): BelongsToMany
    {
        return $this->people()->wherePivot('role', 'director');
    }

    public function writers(): BelongsToMany
    {
        return $this->people()->wherePivot('role', 'writer');
    }

    public function soundtracks(): HasMany
    {
        return $this->hasMany(Soundtrack::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function posters(): MorphMany
    {
        return $this->images()->where('type', 'poster');
    }

    public function backdrops(): MorphMany
    {
        return $this->images()->where('type', 'backdrop');
    }

    public function logos(): MorphMany
    {
        return $this->images()->where('type', 'logo');
    }

    // Métodos para obtener contenido en español
    public function getDisplayTitleAttribute(): string
    {
        return $this->title_es ?: $this->title;
    }

    public function getDisplayOverviewAttribute(): ?string
    {
        return $this->overview_es ?: $this->overview;
    }

    public function getDisplaySynopsisAttribute(): ?string
    {
        return $this->synopsis_es ?: $this->synopsis;
    }

    public function getDisplayTaglineAttribute(): ?string
    {
        return $this->tagline_es ?: $this->tagline;
    }
}
