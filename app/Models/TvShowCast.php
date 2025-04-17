<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TvShowCast extends Model
{
    use HasFactory;

    protected $fillable = [
        'tv_show_id',
        'person_id',
        'character',
        'order',
    ];

    public function tvShow(): BelongsTo
    {
        return $this->belongsTo(TvShow::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}