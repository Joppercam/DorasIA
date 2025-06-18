<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActorContentComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'actor_content_id',
        'user_id',
        'parent_id',
        'content',
        'is_approved',
        'edited_at'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'edited_at' => 'datetime'
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
     * Relación con comentario padre (para respuestas)
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Relación con respuestas (comentarios hijos)
     */
    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id')->where('is_approved', true)->orderBy('created_at', 'asc');
    }

    /**
     * Scope para comentarios principales (no respuestas)
     */
    public function scopeMainComments($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope para comentarios aprobados
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Verificar si el comentario ha sido editado
     */
    public function getIsEditedAttribute()
    {
        return !is_null($this->edited_at);
    }

    /**
     * Obtener tiempo transcurrido desde la creación
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Marcar comentario como editado
     */
    public function markAsEdited()
    {
        $this->update(['edited_at' => now()]);
    }
}