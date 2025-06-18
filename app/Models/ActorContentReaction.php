<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActorContentReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'actor_content_id',
        'user_id',
        'type'
    ];

    /**
     * Tipos de reacciones disponibles
     */
    const TYPES = [
        'like' => '👍',
        'dislike' => '👎', 
        'love' => '❤️'
    ];

    /**
     * Relación con el contenido de actor
     */
    public function actorContent()
    {
        return $this->belongsTo(ActorContent::class);
    }

    /**
     * Relación con el usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener emoji de la reacción
     */
    public function getEmojiAttribute()
    {
        return self::TYPES[$this->type] ?? '👍';
    }

    /**
     * Obtener nombre de la reacción
     */
    public function getNameAttribute()
    {
        $names = [
            'like' => 'Me gusta',
            'dislike' => 'No me gusta',
            'love' => 'Me encanta'
        ];

        return $names[$this->type] ?? 'Me gusta';
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}