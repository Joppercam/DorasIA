<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'original_name',
        'slug',
        'biography',
        'profile_path',
        'birthday',
        'deathday',
        'place_of_birth',
        'gender',
        'popularity',
        'api_id',
        'api_source',
    ];

    protected $casts = [
        'birthday' => 'date',
        'deathday' => 'date',
        'popularity' => 'float',
    ];

    public function movieCast(): HasMany
    {
        return $this->hasMany(MovieCast::class);
    }

    public function movieCrew(): HasMany
    {
        return $this->hasMany(MovieCrew::class);
    }

    public function tvShowCast(): HasMany
    {
        return $this->hasMany(TvShowCast::class);
    }

    public function tvShowCrew(): HasMany
    {
        return $this->hasMany(TvShowCrew::class);
    }
}