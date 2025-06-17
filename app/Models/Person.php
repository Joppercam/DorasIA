<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_es',
        'known_for_department',
        'biography',
        'biography_es',
        'birthday',
        'deathday',
        'place_of_birth',
        'place_of_birth_es',
        'profile_path',
        'imdb_id',
        'tmdb_id',
        'popularity',
        'adult',
        'homepage',
        'also_known_as',
        'gender'
    ];

    protected $casts = [
        'birthday' => 'date',
        'deathday' => 'date',
        'popularity' => 'decimal:3',
        'adult' => 'boolean',
        'also_known_as' => 'array'
    ];

    public function series(): BelongsToMany
    {
        return $this->belongsToMany(Series::class, 'series_person')
                    ->withPivot(['role', 'character', 'order', 'department', 'job'])
                    ->withTimestamps();
    }

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movie_person')
                    ->withPivot(['character', 'department', 'job', 'order'])
                    ->withTimestamps();
    }

    // Alias for series to match controller expectations
    public function titles(): BelongsToMany
    {
        return $this->series();
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function profileImages(): MorphMany
    {
        return $this->images()->where('type', 'profile');
    }
    
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    
    /**
     * Get display biography (Spanish if available, otherwise English)
     */
    public function getDisplayBiographyAttribute(): ?string
    {
        return $this->biography_es ?: $this->biography;
    }
    
    /**
     * Get display place of birth (Spanish if available, otherwise English)
     */
    public function getDisplayPlaceOfBirthAttribute(): ?string
    {
        return $this->place_of_birth_es ?: $this->place_of_birth;
    }
    
    /**
     * Get display name (Spanish if available, otherwise original)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name_es ?: $this->name;
    }

    public function followers()
    {
        return $this->hasMany(ActorFollow::class);
    }

    public function followerUsers()
    {
        return $this->belongsToMany(User::class, 'actor_follows', 'person_id', 'user_id')
            ->withTimestamps();
    }

    public function getFollowersCountAttribute()
    {
        return $this->followers()->count();
    }
}
