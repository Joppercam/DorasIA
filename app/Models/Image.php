<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'imageable_type',
        'imageable_id',
        'type',
        'file_path',
        'original_name',
        'width',
        'height',
        'aspect_ratio',
        'vote_average',
        'vote_count',
        'iso_639_1'
    ];

    protected $casts = [
        'vote_average' => 'decimal:1'
    ];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getFullUrlAttribute(): string
    {
        return config('tmdb.image_base_url') . '/original' . $this->file_path;
    }

    public function getUrlAttribute(): string
    {
        return $this->getFullUrlAttribute();
    }
}
