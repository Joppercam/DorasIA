<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;

class ChileanHelper
{
    /**
     * Verifica si el usuario es de Chile
     */
    public static function isChileanUser(): bool
    {
        return Session::get('is_chilean', true);
    }
    
    /**
     * Obtiene términos K-drama adaptados para Chile
     */
    public static function getKdramaTerm(string $term): string
    {
        $terms = config('chilean.kdrama_terms', []);
        return $terms[$term] ?? $term;
    }
    
    /**
     * Obtiene traducciones específicas chilenas
     */
    public static function getChileanTranslation(string $key): string
    {
        $translations = config('chilean.chilean_translations', []);
        return $translations[$key] ?? $key;
    }
    
    /**
     * Obtiene frases chilenas para la interfaz
     */
    public static function getChileanPhrase(string $key): string
    {
        $phrases = config('chilean.chilean_phrases', []);
        return $phrases[$key] ?? $key;
    }
    
    /**
     * Formatea fecha para horario chileno
     */
    public static function formatChileanDate($date, string $format = 'd/m/Y H:i'): string
    {
        if (!$date) return '';
        
        return \Carbon\Carbon::parse($date)
            ->setTimezone('America/Santiago')
            ->format($format);
    }
    
    /**
     * Obtiene géneros populares en Chile
     */
    public static function getPopularGenres(): array
    {
        return config('chilean.popular_genres', []);
    }
    
    /**
     * Personaliza el saludo según la hora chilena
     */
    public static function getChileanGreeting(): string
    {
        $hour = \Carbon\Carbon::now('America/Santiago')->hour;
        
        if ($hour < 12) {
            return '¡Buenos días, fanática!';
        } elseif ($hour < 18) {
            return '¡Buenas tardes!';
        } else {
            return '¡Buenas noches, hora de K-dramas!';
        }
    }
}