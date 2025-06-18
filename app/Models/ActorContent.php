<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActorContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_id',
        'type',
        'title',
        'content',
        'media_url',
        'media_type',
        'thumbnail_url',
        'duration',
        'is_exclusive',
        'is_featured',
        'published_at',
        'source',
        'external_url',
        'external_video_url',
        'external_video_type',
        'tags',
        'view_count',
        'like_count',
        'metadata'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_exclusive' => 'boolean',
        'is_featured' => 'boolean',
        'tags' => 'array',
        'metadata' => 'array'
    ];

    /**
     * Tipos de contenido disponibles
     */
    const TYPES = [
        'interview' => 'Entrevista',
        'behind_scenes' => 'Behind the Scenes',
        'biography' => 'Biografía',
        'news' => 'Noticia',
        'gallery' => 'Galería',
        'video' => 'Video',
        'article' => 'Artículo',
        'timeline' => 'Línea de Tiempo',
        'trivia' => 'Curiosidades',
        'social' => 'Redes Sociales'
    ];

    /**
     * Relación con el actor
     */
    public function actor()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    /**
     * Relación con likes de usuarios
     */
    public function likes()
    {
        return $this->hasMany(ActorContentLike::class);
    }

    /**
     * Relación con vistas de usuarios
     */
    public function views()
    {
        return $this->hasMany(ActorContentView::class);
    }

    /**
     * Relación con reacciones de usuarios
     */
    public function reactions()
    {
        return $this->hasMany(ActorContentReaction::class);
    }

    /**
     * Relación con comentarios
     */
    public function comments()
    {
        return $this->hasMany(ActorContentComment::class)->approved()->mainComments()->with('replies', 'user')->orderBy('created_at', 'desc');
    }

    /**
     * Obtener conteo de reacciones por tipo
     */
    public function getReactionCountsAttribute()
    {
        return $this->reactions()
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }

    /**
     * Obtener conteo total de comentarios (incluyendo respuestas)
     */
    public function getCommentsCountAttribute()
    {
        return $this->hasMany(ActorContentComment::class)->approved()->count();
    }

    /**
     * Scopes para filtrado
     */
    public function scopeExclusive($query)
    {
        return $query->where('is_exclusive', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', now());
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('published_at', '>=', now()->subDays($days));
    }

    /**
     * Incrementar contador de vistas
     */
    public function incrementViews($userId = null)
    {
        $this->increment('view_count');
        
        if ($userId) {
            $this->views()->firstOrCreate([
                'user_id' => $userId,
                'viewed_at' => now()
            ]);
        }
    }

    /**
     * Obtener duración formateada
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) return null;
        
        $minutes = intval($this->duration / 60);
        $seconds = $this->duration % 60;
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Obtener tipo de contenido traducido
     */
    public function getTypeNameAttribute()
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Verificar si el usuario ha dado like
     */
    public function hasLikedByUser($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Toggle like de usuario
     */
    public function toggleLike($userId)
    {
        $like = $this->likes()->where('user_id', $userId)->first();
        
        if ($like) {
            $like->delete();
            $this->decrement('like_count');
            return false;
        } else {
            $this->likes()->create(['user_id' => $userId]);
            $this->increment('like_count');
            return true;
        }
    }

    /**
     * Agregar o actualizar reacción de usuario
     */
    public function addReaction($userId, $type)
    {
        // Verificar que el tipo es válido
        if (!in_array($type, ['like', 'dislike', 'love'])) {
            return false;
        }

        // Buscar reacción existente del usuario
        $existingReaction = $this->reactions()->where('user_id', $userId)->first();

        if ($existingReaction) {
            if ($existingReaction->type === $type) {
                // Si es la misma reacción, la eliminamos (toggle)
                $existingReaction->delete();
                return null;
            } else {
                // Si es diferente, la actualizamos
                $existingReaction->update(['type' => $type]);
                return $type;
            }
        } else {
            // Crear nueva reacción
            $this->reactions()->create([
                'user_id' => $userId,
                'type' => $type
            ]);
            return $type;
        }
    }

    /**
     * Obtener reacción del usuario actual
     */
    public function getUserReaction($userId)
    {
        if (!$userId) return null;
        
        $reaction = $this->reactions()->where('user_id', $userId)->first();
        return $reaction ? $reaction->type : null;
    }

    /**
     * Obtener icono del tipo de contenido
     */
    public function getTypeIcon()
    {
        $icons = [
            'interview' => '🎤',
            'behind_scenes' => '🎬',
            'biography' => '📖',
            'news' => '📰',
            'gallery' => '📸',
            'video' => '🎥',
            'article' => '📝',
            'timeline' => '📅',
            'trivia' => '💡',
            'social' => '📱'
        ];

        return $icons[$this->type] ?? '📄';
    }

    /**
     * Obtener duración en minutos
     */
    public function getDurationMinutesAttribute()
    {
        return $this->duration ? round($this->duration / 60) : null;
    }

    /**
     * Verificar si tiene video externo
     */
    public function hasExternalVideo()
    {
        return !empty($this->external_video_url);
    }

    /**
     * Obtener embed code para video externo
     */
    public function getExternalVideoEmbed()
    {
        if (!$this->hasExternalVideo()) {
            return null;
        }

        switch ($this->external_video_type) {
            case 'tiktok':
                return $this->getTikTokEmbed();
            case 'youtube':
                return $this->getYouTubeEmbed();
            case 'instagram':
                return $this->getInstagramEmbed();
            default:
                return null;
        }
    }

    /**
     * Generar embed de TikTok
     */
    private function getTikTokEmbed()
    {
        // Extraer ID del video de TikTok
        preg_match('/video\/(\d+)/', $this->external_video_url, $matches);
        $videoId = $matches[1] ?? null;

        if (!$videoId) {
            return null;
        }

        return '<blockquote class="tiktok-embed" cite="' . $this->external_video_url . '" data-video-id="' . $videoId . '" style="max-width: 605px;min-width: 325px;" >
                <section></section>
                </blockquote>
                <script async src="https://www.tiktok.com/embed.js"></script>';
    }

    /**
     * Generar embed de YouTube
     */
    private function getYouTubeEmbed()
    {
        // Extraer ID del video de YouTube
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->external_video_url, $matches);
        $videoId = $matches[1] ?? null;

        if (!$videoId) {
            return null;
        }

        return '<div class="youtube-embed" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                <iframe src="https://www.youtube.com/embed/' . $videoId . '" 
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                </iframe>
                </div>';
    }

    /**
     * Generar embed de Instagram
     */
    private function getInstagramEmbed()
    {
        return '<blockquote class="instagram-media" data-instgrm-permalink="' . $this->external_video_url . '" data-instgrm-version="14">
                </blockquote>
                <script async src="//www.instagram.com/embed.js"></script>';
    }

    /**
     * Obtener thumbnail de video externo
     */
    public function getExternalVideoThumbnail()
    {
        if (!$this->hasExternalVideo()) {
            return $this->thumbnail_url;
        }

        switch ($this->external_video_type) {
            case 'youtube':
                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->external_video_url, $matches);
                $videoId = $matches[1] ?? null;
                return $videoId ? "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg" : $this->thumbnail_url;
            
            case 'tiktok':
            case 'instagram':
            default:
                return $this->thumbnail_url;
        }
    }
}