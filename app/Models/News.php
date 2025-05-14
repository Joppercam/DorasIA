<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'source_url',
        'source_name',
        'featured',
        'published_at',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the people related to this news article.
     */
    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'news_person')
            ->withPivot('primary_subject')
            ->withTimestamps();
    }

    /**
     * Get the primary subject(s) of this news article.
     */
    public function primarySubjects()
    {
        return $this->people()->wherePivot('primary_subject', true);
    }
}
