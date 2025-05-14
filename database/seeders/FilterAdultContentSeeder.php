<?php

namespace Database\Seeders;

use App\Models\Title;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FilterAdultContentSeeder extends Seeder
{
    /**
     * Filtrar el contenido para adultos y traducir títulos.
     */
    public function run(): void
    {
        // Lista de palabras que podrían indicar contenido para adultos
        $adultKeywords = [
            '욕망', '노출', '새엄마', '가슴', '친구의 아내', '여자친구', '친구의 엄마', 
            '물오징어', '마누라', '엄마친구', '정력대왕', '시아버지', '탐하다', '직장 연애사',
            'adult', 'XXX', '여직원들', '바꿔먹기', '마누라 바꿔먹기'
        ];
        
        // Obtener todos los títulos
        $titles = Title::all();
        
        // Contador de títulos eliminados y actualizados
        $removedCount = 0;
        $translatedCount = 0;
        
        foreach ($titles as $title) {
            $isAdultContent = false;
            
            // Comprobar si el título contiene alguna de las palabras clave
            foreach ($adultKeywords as $keyword) {
                if (Str::contains($title->title, $keyword) || 
                    Str::contains($title->original_title, $keyword) || 
                    Str::contains($title->synopsis, $keyword)) {
                    $isAdultContent = true;
                    break;
                }
            }
            
            // Si es contenido para adultos, eliminarlo
            if ($isAdultContent) {
                // Eliminar también las relaciones
                DB::table('title_genre')->where('title_id', $title->id)->delete();
                DB::table('title_person')->where('title_id', $title->id)->delete();
                $title->delete();
                $removedCount++;
                continue;
            }
            
            // Si el título no está en español, intentamos traducirlo
            $needsTranslation = !$this->isSpanish($title->title);
            
            if ($needsTranslation && !empty($title->original_title)) {
                // Si tenemos el título original, verificamos si ya tiene una traducción al español
                // en la base de datos de TMDB, que a menudo incluye traducciones
                if ($title->original_title != $title->title) {
                    // Si el título es diferente del original, asumimos que ya es una traducción
                    // así que no hacemos nada
                } else {
                    // Si no tiene traducción, generamos un título genérico basado en el tipo y origen
                    $prefix = '';
                    
                    if ($title->type === 'series') {
                        switch ($title->country) {
                            case 'Corea del Sur':
                                $prefix = 'K-Drama: ';
                                break;
                            case 'Japón':
                                $prefix = 'J-Drama: ';
                                break;
                            case 'China':
                            case 'Taiwán':
                            case 'Hong Kong':
                                $prefix = 'C-Drama: ';
                                break;
                            default:
                                $prefix = 'Serie: ';
                        }
                    } else {
                        // Para películas
                        switch ($title->country) {
                            case 'Corea del Sur':
                                $prefix = 'Película Coreana: ';
                                break;
                            case 'Japón':
                                $prefix = 'Película Japonesa: ';
                                break;
                            case 'China':
                            case 'Taiwán':
                            case 'Hong Kong':
                                $prefix = 'Película China: ';
                                break;
                            default:
                                $prefix = 'Película: ';
                        }
                    }
                    
                    // Guardamos el título original y creamos uno nuevo en español
                    $title->title = $prefix . $title->original_title;
                    $title->save();
                    $translatedCount++;
                }
            }
            
            // Actualizar marcado como featured solo para contenido de calidad
            if ($title->vote_average < 6.0) {
                $title->featured = false;
                $title->save();
            }
        }
        
        $this->command->info("Proceso completado: {$removedCount} títulos para adultos eliminados y {$translatedCount} títulos traducidos.");
    }
    
    /**
     * Verificar si un texto está en español (aproximadamente).
     */
    private function isSpanish($text): bool
    {
        // Palabras comunes en español
        $spanishWords = [
            'el', 'la', 'los', 'las', 'un', 'una', 'unos', 'unas', 'y', 'o', 'de', 'del', 'al', 'a', 'en', 'con', 'por', 'para',
            'este', 'esta', 'estos', 'estas', 'ese', 'esa', 'esos', 'esas', 'aquel', 'aquella', 'aquellos', 'aquellas', 
            'mi', 'tu', 'su', 'nuestro', 'vuestro', 'mis', 'tus', 'sus', 'nuestros', 'vuestros',
            'sí', 'no', 'día', 'noche', 'amor', 'vida', 'tiempo', 'nuevo', 'bueno', 'malo', 'grande', 'pequeño',
            'mujer', 'hombre', 'niño', 'niña', 'casa', 'ciudad', 'rey', 'héroe', 'juego', 'tierra', 'mar', 'mundo',
            'película', 'serie', 'historia', 'aventura'
        ];
        
        // Caracteres especiales del español
        $spanishChars = ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', '¿', '¡'];
        
        // Verificar si contiene caracteres especiales del español
        foreach ($spanishChars as $char) {
            if (Str::contains(strtolower($text), $char)) {
                return true;
            }
        }
        
        // Dividir el texto en palabras
        $words = preg_split('/\s+/', strtolower($text));
        
        // Contar palabras en español
        $spanishWordCount = 0;
        foreach ($words as $word) {
            // Limpiar la palabra de signos de puntuación
            $word = preg_replace('/[^\p{L}\p{N}]/u', '', $word);
            
            if (in_array($word, $spanishWords)) {
                $spanishWordCount++;
            }
        }
        
        // Si al menos el 20% de las palabras son reconocibles como español, consideramos que está en español
        return (count($words) > 0) && ($spanishWordCount / count($words) >= 0.2);
    }
}