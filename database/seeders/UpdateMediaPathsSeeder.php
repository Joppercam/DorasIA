<?php

namespace Database\Seeders;

use App\Models\Title;
use App\Models\Category;
use Illuminate\Database\Seeder;

class UpdateMediaPathsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Actualizar las rutas de las imágenes de los títulos
        $titles = Title::all();
        
        foreach ($titles as $index => $title) {
            // Usar una imagen existente para los pósters
            $posterIndex = ($index % 10) + 1;
            $title->poster = "posters/poster-{$posterIndex}.jpg";
            
            // Usar una imagen existente para los backdrops
            $backdropIndex = ($index % 5) + 1;
            $title->backdrop = "backdrops/backdrop-{$backdropIndex}.jpg";
            
            $title->save();
            
            $this->command->info("Actualizado título: {$title->title}");
        }
        
        // Actualizar las imágenes de las categorías
        $categories = Category::all();
        
        foreach ($categories as $category) {
            // Asignar imágenes a las categorías basadas en sus slugs
            $category->image = "images/categories/{$category->slug}.jpg";
            $category->hero_image = "images/heroes/{$category->slug}.jpg";
            
            $category->save();
            
            $this->command->info("Actualizada categoría: {$category->name}");
        }
        
        $this->command->info("Se han actualizado todas las rutas de imágenes.");
    }
}