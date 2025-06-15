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

    public function ratings(): HasMany
    {
        return $this->hasMany(TitleRating::class);
    }

    public function watchlistItems(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    
    public function polymorphicComments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function professionalReviews(): HasMany
    {
        return $this->hasMany(ProfessionalReview::class);
    }

    public function userRating($userId = null): ?TitleRating
    {
        if (!$userId) {
            return null;
        }
        return $this->ratings()->where('user_id', $userId)->first();
    }

    public function getRatingCounts(): array
    {
        return [
            'love' => $this->ratings()->where('rating_type', 'love')->count(),
            'like' => $this->ratings()->where('rating_type', 'like')->count(),
            'dislike' => $this->ratings()->where('rating_type', 'dislike')->count(),
        ];
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

    public function episodeProgress()
    {
        return $this->hasMany(EpisodeProgress::class);
    }

    public function getUserSeriesProgress($userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) return null;

        $totalEpisodes = $this->episodes()->count();
        $completedEpisodes = $this->episodeProgress()
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->count();

        $watchingEpisodes = $this->episodeProgress()
            ->where('user_id', $userId)
            ->where('status', 'watching')
            ->count();

        return [
            'total_episodes' => $totalEpisodes,
            'completed_episodes' => $completedEpisodes,
            'watching_episodes' => $watchingEpisodes,
            'progress_percentage' => $totalEpisodes > 0 ? round(($completedEpisodes / $totalEpisodes) * 100, 2) : 0,
            'next_episode' => $this->getNextEpisodeForUser($userId)
        ];
    }

    public function getNextEpisodeForUser($userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) return null;

        // Buscar el primer episodio no completado
        $nextEpisode = $this->episodes()
            ->leftJoin('episode_progress', function($join) use ($userId) {
                $join->on('episodes.id', '=', 'episode_progress.episode_id')
                     ->where('episode_progress.user_id', '=', $userId);
            })
            ->where(function($query) {
                $query->whereNull('episode_progress.status')
                      ->orWhere('episode_progress.status', '!=', 'completed');
            })
            ->orderBy('season_number')
            ->orderBy('episode_number')
            ->select('episodes.*')
            ->first();

        return $nextEpisode;
    }

    public function isInWatchlist($userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) return false;

        return $this->watchlistItems()
            ->where('user_id', $userId)
            ->exists();
    }
}
