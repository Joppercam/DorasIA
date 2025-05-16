<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalReview extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title_id',
        'reviewer_name',
        'reviewer_source',
        'content',
        'rating',
        'review_date',
        'review_url',
        'language',
        'is_featured',
        'external_id'
    ];
    
    protected $casts = [
        'review_date' => 'date',
        'rating' => 'float',
        'is_featured' => 'boolean'
    ];
    
    /**
     * Get the title associated with the review
     */
    public function title()
    {
        return $this->belongsTo(Title::class);
    }
    
    /**
     * Scope for featured reviews
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
    
    /**
     * Scope for reviews by language
     */
    public function scopeByLanguage($query, $language)
    {
        return $query->where('language', $language);
    }
    
    /**
     * Scope for recent reviews
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
    
    /**
     * Get formatted rating out of 5 stars
     */
    public function getRatingStarsAttribute()
    {
        if (!$this->rating) {
            return 0;
        }
        return round($this->rating / 2, 1); // Convert 10-point scale to 5-star scale
    }
    
    /**
     * Get truncated content
     */
    public function getTruncatedContentAttribute($length = 200)
    {
        return \Str::limit($this->content, $length);
    }
}