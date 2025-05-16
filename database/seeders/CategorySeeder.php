<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'K-Drama',
                'slug' => 'k-drama',
                'description' => 'Series de televisión coreanas, conocidas por sus historias emotivas, romance y trama bien desarrollada.',
                'language' => 'ko',
                'country' => 'KR',
                'image' => 'images/categories/k-drama.jpg',
                'hero_image' => 'images/heroes/k-drama.jpg',
                'display_order' => 1,
            ],
            [
                'name' => 'J-Drama',
                'slug' => 'j-drama',
                'description' => 'Series de televisión japonesas que ofrecen una mezcla única de cultura japonesa, trama cuidadosa y actuaciones sutiles.',
                'language' => 'ja',
                'country' => 'JP',
                'image' => 'images/categories/j-drama.jpg',
                'hero_image' => 'images/heroes/j-drama.jpg',
                'display_order' => 2,
            ],
            [
                'name' => 'C-Drama',
                'slug' => 'c-drama',
                'description' => 'Series de televisión chinas, que incluyen dramas históricos, de fantasía y contemporáneos con producción de alta calidad.',
                'language' => 'zh',
                'country' => 'CN',
                'image' => 'images/categories/c-drama.jpg',
                'hero_image' => 'images/heroes/c-drama.jpg',
                'display_order' => 3,
            ],
            [
                'name' => 'Thai Drama',
                'slug' => 'thai-drama',
                'description' => 'Series de televisión tailandesas, conocidas por sus historias románticas y ritmo único.',
                'language' => 'th',
                'country' => 'TH',
                'image' => 'images/categories/dorasia-originals.jpg',
                'hero_image' => 'images/heroes/dorasia-originals.jpg',
                'display_order' => 4,
            ],
            [
                'name' => 'Asian Drama',
                'slug' => 'asian-drama',
                'description' => 'Otros dramas asiáticos de diversos países.',
                'language' => 'en',
                'country' => 'AS',
                'image' => 'images/categories/dorasia-originals.jpg',
                'hero_image' => 'images/heroes/dorasia-originals.jpg',
                'display_order' => 5,
            ],
            [
                'name' => 'Películas',
                'slug' => 'peliculas',
                'description' => 'Una selección de las mejores películas asiáticas de varios países y géneros.',
                'language' => null,
                'country' => 'AS',
                'image' => 'images/categories/peliculas.jpg',
                'hero_image' => 'images/heroes/peliculas.jpg',
                'display_order' => 6,
            ],
            [
                'name' => 'Dorasia Originals',
                'slug' => 'dorasia-originals',
                'description' => 'Contenido exclusivo y recomendaciones especiales de Dorasia.',
                'language' => null,
                'country' => null,
                'image' => 'images/categories/dorasia-originals.jpg',
                'hero_image' => 'images/heroes/dorasia-originals.jpg',
                'display_order' => 0,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        $this->command->info('Categorías creadas correctamente.');
    }
}