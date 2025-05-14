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
        'country',
        'photo',
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
}