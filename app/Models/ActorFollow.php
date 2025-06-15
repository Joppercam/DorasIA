<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActorFollow extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'person_id'
    ];

    /**
     * Relación con el usuario que sigue al actor
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el actor seguido
     */
    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Scope para obtener seguidores de un actor específico
     */
    public function scopeForActor($query, $personId)
    {
        return $query->where('person_id', $personId);
    }

    /**
     * Scope para obtener actores seguidos por un usuario específico
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}