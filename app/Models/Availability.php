<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_type',
        'content_id',
        'platform_id',
        'country_id',
        'available_from',
        'available_until',
        'url',
        'quality',
        'type',
        'price',
    ];

    protected $casts = [
        'available_from' => 'date',
        'available_until' => 'date',
        'price' => 'float',
    ];

    public function content(): MorphTo
    {
        return $this->morphTo();
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}