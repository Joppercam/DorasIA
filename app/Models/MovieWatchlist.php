<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MovieWatchlist extends Model
{
    use HasFactory;

    protected $table = 'movie_watchlist';

    protected $fillable = [
        'user_id',
        'movie_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}