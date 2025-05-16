<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'original_name',
        'biography',
        'birthday',
        'deathday',
        'birth_date', // Legacy field
        'death_date',
        'country',
        'photo',
        'profile_path',
        'slug',
        'tmdb_id',
        'gender',
        'place_of_birth',
        'popularity',
        'imdb_id',
        'instagram_id',
        'twitter_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'birthday' => 'date',
        'deathday' => 'date',
        'popularity' => 'float',
    ];

    /**
     * Get the titles associated with this person.
     */
    public function titles(): BelongsToMany
    {
        return $this->belongsToMany(Title::class, 'title_person')
            ->withPivot('role', 'character', 'order')
            ->withTimestamps();
    }

    /**
     * Get the titles where this person is an actor.
     */
    public function actingTitles(): BelongsToMany
    {
        return $this->belongsToMany(Title::class, 'title_person')
            ->withPivot('role', 'character', 'order')
            ->wherePivot('role', 'actor')
            ->withTimestamps();
    }

    /**
     * Get the titles where this person is a director.
     */
    public function directedTitles(): BelongsToMany
    {
        return $this->belongsToMany(Title::class, 'title_person')
            ->withPivot('role')
            ->wherePivot('role', 'director')
            ->withTimestamps();
    }
    
    /**
     * Get news articles related to this person.
     */
    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'news_person')
            ->withPivot('primary_subject')
            ->withTimestamps();
    }
    
    /**
     * Get news articles where this person is the primary subject.
     */
    public function featuredNews()
    {
        return $this->news()->wherePivot('primary_subject', true);
    }
    
    /**
     * Get most popular people based on popularity score.
     */
    public static function getPopularPeople($limit = 10)
    {
        return self::orderBy('popularity', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get the full profile image path.
     */
    public function getProfilePath()
    {
        if (!$this->profile_path) {
            return null;
        }
        
        // Si es una ruta completa local
        if (str_starts_with($this->profile_path, 'profiles/')) {
            return asset($this->profile_path);
        }
        
        // Si es solo el nombre del archivo
        if (strlen($this->profile_path) > 0 && !str_contains($this->profile_path, '/')) {
            return config('services.tmdb.image_base_url') . 'w185' . '/' . $this->profile_path;
        }
        
        // Si es una ruta de TMDB
        return config('services.tmdb.image_base_url') . 'w185' . $this->profile_path;
    }
}