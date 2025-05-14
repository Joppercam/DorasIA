<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_id',
        'name',
        'number',
        'overview',
        'poster',
        'air_date',
    ];

    protected $casts = [
        'number' => 'integer',
        'air_date' => 'date',
    ];

    /**
     * Get the title that this season belongs to.
     */
    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class);
    }

    /**
     * Get the episodes in this season.
     */
    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class)->orderBy('number');
    }
}