<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Soundtrack extends Model
{
    use HasFactory;

    protected $fillable = [
        'series_id', // Mantener para compatibilidad
        'soundtrackable_type',
        'soundtrackable_id',
        'title',
        'artist',
        'album',
        'lyrics',
        'spotify_url',
        'apple_music_url',
        'youtube_url',
        'youtube_id',
        'preview_url',
        'duration',
        'popularity',
        'is_active',
        'is_main_theme',
        'is_ending_theme',
        'track_number'
    ];

    protected $casts = [
        'is_main_theme' => 'boolean',
        'is_ending_theme' => 'boolean',
        'is_active' => 'boolean',
        'duration' => 'integer',
        'track_number' => 'integer',
        'popularity' => 'decimal:2'
    ];

    // === RELATIONSHIPS ===
    
    /**
     * Relación polimórfica - puede ser Serie o Película
     */
    public function soundtrackable()
    {
        return $this->morphTo();
    }
    
    /**
     * Relación legacy con Series (mantener compatibilidad)
     */
    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    // === ACCESSORS ===
    
    /**
     * Get formatted duration (mm:ss)
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration) {
            return '0:00';
        }
        
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    /**
     * Get display title with artist
     */
    public function getDisplayTitleAttribute(): string
    {
        return "{$this->title} - {$this->artist}";
    }

    /**
     * Get type label in Spanish
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'opening' => 'Opening',
            'ending' => 'Ending',
            'ost' => 'OST',
            'insert_song' => 'Canción Inserción',
            'theme' => 'Tema Principal',
            default => 'OST'
        };
    }

    /**
     * Get cover image URL
     */
    public function getCoverUrlAttribute(): ?string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        
        // Fallback a la imagen de la serie
        return $this->series?->posterUrl();
    }

    /**
     * Get artist image URL
     */
    public function getArtistImageUrlAttribute(): ?string
    {
        if ($this->artist_image) {
            return asset('storage/' . $this->artist_image);
        }
        
        return null;
    }

    // === SCOPES ===
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query, $minPopularity = 1.0)
    {
        return $query->where('popularity', '>', $minPopularity)
                    ->orderBy('popularity', 'desc');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeWithPreview($query)
    {
        return $query->whereNotNull('preview_url')
                    ->orWhereNotNull('youtube_id');
    }

    // === HELPER METHODS ===
    
    /**
     * Check if soundtrack has playable preview
     */
    public function hasPreview(): bool
    {
        return !empty($this->preview_url) || !empty($this->youtube_id);
    }

    /**
     * Get playable URL (priority: preview_url, then YouTube)
     */
    public function getPlayableUrl(): ?string
    {
        if ($this->preview_url) {
            return $this->preview_url;
        }
        
        if ($this->youtube_id) {
            return "https://www.youtube.com/watch?v={$this->youtube_id}";
        }
        
        return null;
    }

    /**
     * Get YouTube embed URL
     */
    public function getYouTubeEmbedUrl(): ?string
    {
        if (!$this->youtube_id) {
            return null;
        }
        
        return "https://www.youtube.com/embed/{$this->youtube_id}?autoplay=0&rel=0";
    }

    /**
     * Increment play count
     */
    public function incrementPlayCount(): void
    {
        $this->increment('play_count');
    }

    /**
     * Get streaming platforms as array
     */
    public function getStreamingPlatforms(): array
    {
        $platforms = [];
        
        if ($this->spotify_url) {
            $platforms['spotify'] = [
                'name' => 'Spotify',
                'url' => $this->spotify_url,
                'icon' => '🎵'
            ];
        }
        
        if ($this->apple_music_url) {
            $platforms['apple'] = [
                'name' => 'Apple Music',
                'url' => $this->apple_music_url,
                'icon' => '🍎'
            ];
        }
        
        if ($this->youtube_url) {
            $platforms['youtube'] = [
                'name' => 'YouTube',
                'url' => $this->youtube_url,
                'icon' => '🎬'
            ];
        }
        
        return $platforms;
    }

    /**
     * Check if has lyrics in any language
     */
    public function hasLyrics(): bool
    {
        return !empty($this->lyrics) || 
               !empty($this->lyrics_spanish) || 
               !empty($this->lyrics_romanized);
    }

    /**
     * Get available lyrics languages
     */
    public function getLyricsLanguages(): array
    {
        $languages = [];
        
        if ($this->lyrics) {
            $languages['original'] = 'Original';
        }
        
        if ($this->lyrics_spanish) {
            $languages['spanish'] = 'Español';
        }
        
        if ($this->lyrics_romanized) {
            $languages['romanized'] = 'Romanizado';
        }
        
        return $languages;
    }
}