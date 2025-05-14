<?php

namespace Database\Seeders;

use App\Models\Title;
use Illuminate\Database\Seeder;

class UpdateFeaturedTitlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Desmarcar todos los títulos como no destacados primero
        Title::query()->update(['featured' => false]);
        
        // Destacar automáticamente algunos títulos populares para mejorar el hero
        $popularTitles = [
            'Parásitos',
            'El juego del calamar',
            'Train to Busan',
            'Old Boy',
            'Memories of Murder',
            'La doncella',
            'Rompenieves',
            'Hunter × Hunter',
            'One Piece',
            'Naruto',
            'Dragon Ball Z'
        ];
        
        $this->command->info("Buscando títulos populares para destacar...");
        $featuredCount = 0;
        
        foreach ($popularTitles as $title) {
            // Buscar por título o título parcial
            $matches = Title::where('title', 'like', "%{$title}%")
                ->whereNotNull('backdrop')
                ->where('backdrop', '!=', '')
                ->get();
            
            foreach ($matches as $match) {
                $match->featured = true;
                $match->save();
                $featuredCount++;
                $this->command->info("Destacado: {$match->title}");
            }
        }
        
        // Destacar los títulos mejor valorados con imágenes válidas
        $topRatedTitles = Title::whereNotNull('backdrop')
            ->where('backdrop', '!=', '')
            ->where('vote_average', '>=', 7.5)
            ->where('featured', false)
            ->take(10)
            ->get();
            
        foreach ($topRatedTitles as $title) {
            $title->featured = true;
            $title->save();
            $featuredCount++;
            $this->command->info("Destacado por valoración: {$title->title} ({$title->vote_average})");
        }
        
        // Asegurarnos de tener al menos algunos títulos de cada categoría
        if (Title::where('featured', true)->count() < 5) {
            $this->command->info("Añadiendo títulos destacados por categoría...");
            
            // Obtener algunos títulos de cada categoría para destacarlos
            $kDramaTitles = Title::whereHas('category', function ($query) {
                $query->where('slug', 'k-drama');
            })
            ->whereNotNull('backdrop')
            ->where('backdrop', '!=', '')
            ->where('featured', false)
            ->take(2)->get();
            
            $jDramaTitles = Title::whereHas('category', function ($query) {
                $query->where('slug', 'j-drama');
            })
            ->whereNotNull('backdrop')
            ->where('backdrop', '!=', '')
            ->where('featured', false)
            ->take(1)->get();
            
            $cDramaTitles = Title::whereHas('category', function ($query) {
                $query->where('slug', 'c-drama');
            })
            ->whereNotNull('backdrop')
            ->where('backdrop', '!=', '')
            ->where('featured', false)
            ->take(1)->get();
            
            $movieTitles = Title::where('type', 'movie')
                ->whereNotNull('backdrop')
                ->where('backdrop', '!=', '')
                ->where('featured', false)
                ->take(1)->get();
            
            // Marcar títulos como destacados
            $additionalTitles = $kDramaTitles->merge($jDramaTitles)
                ->merge($cDramaTitles)
                ->merge($movieTitles);
            
            foreach ($additionalTitles as $title) {
                $title->featured = true;
                $title->save();
                $featuredCount++;
                $this->command->info("Título destacado adicional: {$title->title}");
            }
        }
        
        $this->command->info("Se han destacado {$featuredCount} títulos");
    }
}