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
        'known_for_department',
        'biography',
        'birthday',
        'deathday',
        'place_of_birth',
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

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function profileImages(): MorphMany
    {
        return $this->images()->where('type', 'profile');
    }
}
