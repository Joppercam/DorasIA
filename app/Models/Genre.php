<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
<<<<<<< HEAD
    ];

    /**
     * Get the titles associated with this genre.
     */
    public function titles(): BelongsToMany
    {
        return $this->belongsToMany(Title::class, 'title_genre');
    }
}
=======
        'api_id',
    ];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class);
    }

    public function tvShows(): BelongsToMany
    {
        return $this->belongsToMany(TvShow::class);
    }
}
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
