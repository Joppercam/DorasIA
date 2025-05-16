<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Title extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'original_title',
        'slug',
        'description',
        'release_date',
        'type',
        'tmdb_id',
        'poster_path',
        'backdrop_path',
        'vote_average',
        'vote_count',
        'popularity',
        'category_id',
        'runtime',
        'status',
        'is_featured',
        'is_trending',
        'metadata',
        'streaming_platforms'
    ];

    protected $casts = [
        'release_date' => 'date',
        'runtime' => 'integer',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'vote_average' => 'float',
        'vote_count' => 'integer',
        'popularity' => 'float',
        'tmdb_id' => 'integer',
        'metadata' => 'array',
        'streaming_platforms' => 'array'
    ];

    /**
     * Get the category that the title belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the genres associated with the title.
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'title_genre');
    }

    /**
     * Get the people associated with the title.
     */
    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'title_person')
            ->withPivot('role', 'character', 'order')
            ->withTimestamps();
    }

    /**
     * Get the actors for this title.
     */
    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'title_person')
            ->withPivot('role', 'character', 'order')
            ->wherePivot('role', 'actor')
            ->orderByPivot('order')
            ->withTimestamps();
    }

    /**
     * Get the directors for this title.
     */
    public function directors(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'title_person')
            ->withPivot('role')
            ->wherePivot('role', 'director')
            ->withTimestamps();
    }

    /**
     * Get the seasons for this title.
     */
    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    /**
     * Get the ratings for this title.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get the comments for this title.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get the watchlists that include this title.
     */
    public function watchlists(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }

    /**
     * Get the watch histories for this title.
     */
    public function watchHistories(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }
    
    /**
     * Get the episodes for this title.
     */
    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }
    
    /**
     * Get the total number of episodes for this title
     */
    public function getEpisodesCountAttribute(): int
    {
        if ($this->type !== 'series') {
            return 0;
        }
        
        return $this->seasons->sum(function ($season) {
            return $season->episodes->count();
        });
    }
    
    /**
     * Get poster URL
     */
    public function getPosterUrlAttribute(): string
    {
        if (empty($this->poster_path)) {
            return '/posters/placeholder.jpg';
        }
        
        if (str_starts_with($this->poster_path, 'http')) {
            return $this->poster_path;
        }
        
        // Return the path directly without using asset()
        return '/' . ltrim($this->poster_path, '/');
    }
    
    /**
     * Get poster attribute for backward compatibility
     */
    public function getPosterAttribute(): string
    {
        return $this->poster_path ?? 'posters/placeholder.jpg';
    }
    
    /**
     * Get backdrop URL
     */
    public function getBackdropUrlAttribute(): string
    {
        if (empty($this->backdrop_path)) {
            return '/backdrops/placeholder.jpg';
        }
        
        if (str_starts_with($this->backdrop_path, 'http')) {
            return $this->backdrop_path;
        }
        
        // Return the path directly without using asset()
        return '/' . ltrim($this->backdrop_path, '/');
    }
    
    /**
     * Get backdrop attribute for backward compatibility
     */
    public function getBackdropAttribute(): string
    {
        return $this->backdrop_path ?? 'backdrops/placeholder.jpg';
    }
    
    /**
     * Get trailer URL
     */
    public function getTrailerUrlAttribute(): ?string
    {
        return $this->metadata['trailer_url'] ?? null;
    }
    
    /**
     * Get professional reviews
     */
    public function professionalReviews(): HasMany
    {
        return $this->hasMany(ProfessionalReview::class);
    }
    
    /**
     * Get featured professional reviews
     */
    public function featuredReviews(): HasMany
    {
        return $this->hasMany(ProfessionalReview::class)->featured();
    }
    
    /**
     * Get content rating
     */
    public function getContentRatingAttribute(): ?string
    {
        return $this->metadata['content_rating'] ?? null;
    }
    
    /**
     * Get romantic subgenre
     */
    public function getRomanticSubgenreAttribute(): string
    {
        return $this->metadata['romantic_subgenre'] ?? 'romance';
    }
    
    /**
     * Get the title type (series or movie)
     */
    public function getIsSeriesAttribute(): bool
    {
        return $this->type === 'series';
    }
    
    /**
     * Get the languages
     */
    public function getLanguagesAttribute(): array
    {
        return $this->metadata['languages'] ?? [];
    }
    
    /**
     * Get the origin countries
     */
    public function getOriginCountriesAttribute(): array
    {
        return $this->metadata['origin_countries'] ?? [];
    }
    
    /**
     * Get the main origin country
     */
    public function getMainOriginCountryAttribute(): ?string
    {
        $countries = $this->getOriginCountriesAttribute();
        return !empty($countries) ? $countries[0] : null;
    }
    
    /**
     * Get formatted category name based on main origin country
     */
    public function getFormattedCategoryAttribute(): string
    {
        $country = $this->getMainOriginCountryAttribute();
        
        switch ($country) {
            case 'KR':
                return 'K-Drama';
            case 'JP':
                return 'J-Drama';
            case 'CN':
            case 'TW':
            case 'HK':
                return 'C-Drama';
            case 'TH':
                return 'Thai Drama';
            default:
                return 'Asian Drama';
        }
    }
    
    /**
     * Get romantic dramas scope
     */
    public function scopeRomantic($query)
    {
        return $query->whereHas('genres', function ($q) {
            $q->where('name', 'Romance');
        })->orWhere(function ($q) {
            $q->whereJsonContains('metadata->romantic_subgenre', 'romance')
              ->orWhereJsonContains('metadata->romantic_subgenre', 'historical_romance')
              ->orWhereJsonContains('metadata->romantic_subgenre', 'romantic_comedy')
              ->orWhereJsonContains('metadata->romantic_subgenre', 'melodrama')
              ->orWhereJsonContains('metadata->romantic_subgenre', 'supernatural_romance')
              ->orWhereJsonContains('metadata->romantic_subgenre', 'medical_romance')
              ->orWhereJsonContains('metadata->romantic_subgenre', 'office_romance')
              ->orWhereJsonContains('metadata->romantic_subgenre', 'youth_romance')
              ->orWhereJsonContains('metadata->romantic_subgenre', 'family_romance');
        });
    }
    
    /**
     * Get titles by romantic subgenre scope
     */
    public function scopeByRomanticSubgenre($query, string $subgenre)
    {
        return $query->whereJsonContains('metadata->romantic_subgenre', $subgenre);
    }
    
    /**
     * Get titles by origin country scope
     */
    public function scopeByOriginCountry($query, string $countryCode)
    {
        return $query->whereJsonContains('metadata->origin_countries', $countryCode);
    }
    
    /**
     * Get Korean dramas scope
     */
    public function scopeKorean($query)
    {
        return $query->byOriginCountry('KR');
    }
    
    /**
     * Get Japanese dramas scope
     */
    public function scopeJapanese($query)
    {
        return $query->byOriginCountry('JP');
    }
    
    /**
     * Get Chinese dramas scope
     */
    public function scopeChinese($query)
    {
        return $query->where(function($q) {
            $q->byOriginCountry('CN')
              ->orWhere->byOriginCountry('TW')
              ->orWhere->byOriginCountry('HK');
        });
    }
    
    /**
     * Get titles by streaming platform scope
     */
    public function scopeByStreamingPlatform($query, string $platform)
    {
        return $query->whereJsonContains('streaming_platforms', $platform);
    }
    
    /**
     * Update the average rating for this title
     */
    public function updateAverageRating(): void
    {
        $avgRating = $this->ratings()->avg('score');
        $count = $this->ratings()->count();
        
        $this->update([
            'vote_average' => $avgRating ?: 0,
            'vote_count' => $count
        ]);
    }
}