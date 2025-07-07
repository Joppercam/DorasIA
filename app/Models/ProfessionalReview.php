<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalReview extends Model
{
    protected $fillable = [
        'series_id',
        'movie_id',
        'reviewable_type',
        'source',
        'source_url',
        'author',
        'author_url',
        'rating',
        'max_rating',
        'content',
        'content_es',
        'excerpt',
        'excerpt_es',
        'review_date',
        'is_positive',
        'language',
        'tmdb_review_id'
    ];

    protected $casts = [
        'review_date' => 'date',
        'is_positive' => 'boolean',
        'rating' => 'decimal:1',
        'max_rating' => 'integer'
    ];

    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    // Polimorphic relationship helper
    public function reviewable()
    {
        if ($this->reviewable_type === 'movie') {
            return $this->movie();
        }
        return $this->series();
    }

    // Get the actual reviewable model
    public function getReviewableAttribute()
    {
        if ($this->reviewable_type === 'movie') {
            return $this->movie;
        }
        return $this->series;
    }

    // Getters para contenido en español
    public function getDisplayContentAttribute()
    {
        // Priorizar contenido traducido al español
        if ($this->content_es) {
            return $this->content_es;
        }
        
        // Si no hay traducción, verificar si el contenido original es en español
        if ($this->content && $this->isSpanishContent($this->content)) {
            return $this->content;
        }
        
        // No mostrar contenido en otros idiomas
        return null;
    }

    public function getDisplayExcerptAttribute()
    {
        // Priorizar excerpt traducido al español
        if ($this->excerpt_es) {
            return $this->excerpt_es;
        }
        
        // Si no hay traducción, verificar si el excerpt original es en español
        if ($this->excerpt && $this->isSpanishContent($this->excerpt)) {
            return $this->excerpt;
        }
        
        // No mostrar excerpt en otros idiomas
        return null;
    }

    /**
     * Detectar si el contenido está en español usando palabras comunes y patrones
     */
    private function isSpanishContent($text): bool
    {
        if (empty($text)) {
            return false;
        }
        
        $text = strtolower($text);
        
        // Palabras comunes en español
        $spanishWords = [
            'película', 'serie', 'episodio', 'personaje', 'historia', 'drama',
            'comedia', 'acción', 'terror', 'suspense', 'romance', 'fantasia',
            'ciencia', 'ficción', 'documental', 'animación', 'musical',
            'que', 'con', 'una', 'pero', 'muy', 'más', 'como', 'por', 'para',
            'este', 'esta', 'estos', 'estas', 'todo', 'todos', 'toda', 'todas',
            'año', 'años', 'tiempo', 'vida', 'mundo', 'parte', 'lugar',
            'manera', 'forma', 'caso', 'momento', 'ejemplo', 'problema',
            'es', 'son', 'está', 'están', 'fue', 'fueron', 'será', 'serán',
            'tiene', 'tienen', 'había', 'habían', 'hay', 'hace', 'hacen',
            'puede', 'pueden', 'debe', 'deben', 'quiere', 'quieren',
            'bien', 'mal', 'mejor', 'peor', 'grande', 'pequeño', 'nuevo',
            'viejo', 'joven', 'bueno', 'malo', 'interesante', 'aburrido'
        ];
        
        // Palabras comunes en inglés que indican contenido no español
        $englishWords = [
            'the', 'and', 'is', 'are', 'was', 'were', 'have', 'has', 'had',
            'will', 'would', 'could', 'should', 'might', 'can', 'cannot',
            'this', 'that', 'these', 'those', 'with', 'from', 'they', 'them',
            'their', 'there', 'where', 'when', 'what', 'which', 'who', 'how',
            'movie', 'film', 'series', 'episode', 'character', 'story',
            'drama', 'comedy', 'action', 'horror', 'romance', 'fantasy',
            'science', 'fiction', 'documentary', 'animation', 'musical',
            'good', 'bad', 'better', 'worse', 'best', 'worst', 'great',
            'amazing', 'terrible', 'awesome', 'wonderful', 'perfect',
            'disappointing', 'boring', 'interesting', 'excellent'
        ];
        
        // Contar palabras en español vs inglés
        $spanishMatches = 0;
        $englishMatches = 0;
        $totalWords = 0;
        
        // Extraer palabras (solo letras, sin números ni símbolos)
        $words = preg_split('/[^\p{L}]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        foreach ($words as $word) {
            if (strlen($word) >= 3) { // Solo palabras de 3+ caracteres
                $totalWords++;
                
                if (in_array($word, $spanishWords)) {
                    $spanishMatches++;
                }
                
                if (in_array($word, $englishWords)) {
                    $englishMatches++;
                }
            }
        }
        
        // Si hay pocas palabras, ser más estricto
        if ($totalWords < 10) {
            return $englishMatches === 0 && $spanishMatches > 0;
        }
        
        // Para textos más largos, usar ratio
        $spanishRatio = $totalWords > 0 ? $spanishMatches / $totalWords : 0;
        $englishRatio = $totalWords > 0 ? $englishMatches / $totalWords : 0;
        
        // Considerar español si:
        // - Más palabras en español que en inglés
        // - Al menos 10% de palabras son españolas Y menos del 5% son inglesas
        return ($spanishMatches > $englishMatches) || 
               ($spanishRatio >= 0.1 && $englishRatio < 0.05);
    }

    // Calculate percentage rating
    public function getRatingPercentageAttribute()
    {
        if (!$this->rating || !$this->max_rating) {
            return null;
        }
        return ($this->rating / $this->max_rating) * 100;
    }

    // Determine sentiment based on rating
    public function getSentimentAttribute()
    {
        if (!$this->rating_percentage) {
            return $this->is_positive ? 'positive' : 'negative';
        }
        
        if ($this->rating_percentage >= 70) {
            return 'positive';
        } elseif ($this->rating_percentage >= 40) {
            return 'mixed';
        } else {
            return 'negative';
        }
    }

    /**
     * Scope para solo mostrar reviews con contenido en español
     */
    public function scopeSpanishContent($query)
    {
        return $query->where(function($q) {
            $q->whereNotNull('content_es')
              ->orWhereNotNull('excerpt_es')
              ->orWhere(function($subq) {
                  // Solo incluir reviews donde el contenido original sea detectado como español
                  // En la práctica, esto requerirá evaluar cada review individualmente
                  // Por ahora, filtrar por idioma y validar en el accessor
                  $subq->where('language', 'es');
              });
        });
    }

    /**
     * Verificar si esta review tiene contenido válido en español
     */
    public function hasSpanishContent(): bool
    {
        return !empty($this->display_content) || !empty($this->display_excerpt);
    }
}