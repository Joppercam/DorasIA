<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    public function availability(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }
}