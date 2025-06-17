<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalReview extends Model
{
    protected $fillable = [
        'series_id',
        'movie_id',
        'reviewable_type',
        'source',
        'source_url',
        'author',
        'author_url',
        'rating',
        'max_rating',
        'content',
        'content_es',
        'excerpt',
        'excerpt_es',
        'review_date',
        'is_positive',
        'language',
        'tmdb_review_id'
    ];

    protected $casts = [
        'review_date' => 'date',
        'is_positive' => 'boolean',
        'rating' => 'decimal:1',
        'max_rating' => 'integer'
    ];

    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    // Polimorphic relationship helper
    public function reviewable()
    {
        if ($this->reviewable_type === 'movie') {
            return $this->movie();
        }
        return $this->series();
    }

    // Get the actual reviewable model
    public function getReviewableAttribute()
    {
        if ($this->reviewable_type === 'movie') {
            return $this->movie;
        }
        return $this->series;
    }

    // Getters para contenido en español
    public function getDisplayContentAttribute()
    {
        return $this->content_es ?: $this->content;
    }

    public function getDisplayExcerptAttribute()
    {
        return $this->excerpt_es ?: $this->excerpt;
    }

    // Calculate percentage rating
    public function getRatingPercentageAttribute()
    {
        if (!$this->rating || !$this->max_rating) {
            return null;
        }
        return ($this->rating / $this->max_rating) * 100;
    }

    // Determine sentiment based on rating
    public function getSentimentAttribute()
    {
        if (!$this->rating_percentage) {
            return $this->is_positive ? 'positive' : 'negative';
        }
        
        if ($this->rating_percentage >= 70) {
            return 'positive';
        } elseif ($this->rating_percentage >= 40) {
            return 'mixed';
        } else {
            return 'negative';
        }
    }
}