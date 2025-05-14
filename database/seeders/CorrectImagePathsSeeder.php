<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CorrectImagePathsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Corrigiendo rutas de imágenes...');

        // Corregir rutas de imágenes en títulos
        $titles = DB::table('titles')->get();
        
        foreach ($titles as $title) {
            $poster = $title->poster;
            $backdrop = $title->backdrop;

            // Verificar si ya tiene el prefijo storage/
            if ($poster && !str_starts_with($poster, 'storage/')) {
                $poster = 'storage/' . $poster;
            }
            
            if ($backdrop && !str_starts_with($backdrop, 'storage/')) {
                $backdrop = 'storage/' . $backdrop;
            }

            DB::table('titles')
                ->where('id', $title->id)
                ->update([
                    'poster' => $poster,
                    'backdrop' => $backdrop,
                ]);
        }

        // Corregir rutas de imágenes en temporadas
        $seasons = DB::table('seasons')->get();
        
        foreach ($seasons as $season) {
            $poster = $season->poster;

            // Verificar si ya tiene el prefijo storage/
            if ($poster && !str_starts_with($poster, 'storage/')) {
                $poster = 'storage/' . $poster;
                
                DB::table('seasons')
                    ->where('id', $season->id)
                    ->update([
                        'poster' => $poster,
                    ]);
            }
        }

        // Corregir rutas de imágenes en categorías
        $categories = DB::table('categories')->get();
        
        foreach ($categories as $category) {
            $image = $category->image;
            $heroImage = $category->hero_image;

            // Verificar si ya tiene el prefijo storage/
            if ($image && !str_starts_with($image, 'storage/')) {
                $image = 'storage/' . $image;
            }
            
            if ($heroImage && !str_starts_with($heroImage, 'storage/')) {
                $heroImage = 'storage/' . $heroImage;
            }

            DB::table('categories')
                ->where('id', $category->id)
                ->update([
                    'image' => $image,
                    'hero_image' => $heroImage,
                ]);
        }

        // Corregir rutas de imágenes en personas
        $people = DB::table('people')->get();
        
        foreach ($people as $person) {
            $photo = $person->photo;

            // Verificar si ya tiene el prefijo storage/
            if ($photo && !str_starts_with($photo, 'storage/')) {
                $photo = 'storage/' . $photo;
                
                DB::table('people')
                    ->where('id', $person->id)
                    ->update([
                        'photo' => $photo,
                    ]);
            }
        }

        $this->command->info('Rutas de imágenes corregidas correctamente.');
    }
}