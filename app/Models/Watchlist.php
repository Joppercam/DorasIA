<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Watchlist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'profile_id',
        'title_id',
        'category',
        'position',
        'priority',
        'notes',
        'liked',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'liked' => 'boolean',
        'position' => 'integer',
    ];

    /**
     * Available watchlist categories.
     */
    public const CATEGORIES = [
        'default' => 'Mi Lista',
        'watch_soon' => 'Ver Pronto',
        'watch_later' => 'Ver Más Tarde',
        'favorites' => 'Favoritos',
        'in_progress' => 'Viendo Ahora',
        'completed' => 'Completados',
    ];

    /**
     * Available priority levels.
     */
    public const PRIORITIES = [
        'high' => 'Alta',
        'medium' => 'Media',
        'low' => 'Baja',
    ];

    /**
     * Get the profile that owns the watchlist item.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the title on the watchlist.
     */
    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class);
    }

    /**
     * Get the translated category name.
     *
     * @return string
     */
    public function getCategoryNameAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? 'Mi Lista';
    }

    /**
     * Get the translated priority name.
     *
     * @return string
     */
    public function getPriorityNameAttribute(): string
    {
        return self::PRIORITIES[$this->priority] ?? 'Media';
    }

    /**
     * Scope a query to only include items in a specific category.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to only include items with a specific priority.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $priority
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query to only include liked items.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLiked($query)
    {
        return $query->where('liked', true);
    }

    /**
     * Sort items by position.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByPosition($query)
    {
        return $query->orderBy('position', 'asc');
    }
}