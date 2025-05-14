<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateStoragePathsSeeder extends Seeder
{
    /**
     * Update media paths to use Laravel's storage system
     */
    public function run(): void
    {
        // Update titles
        $titles = DB::table('titles')->get();
        
        foreach ($titles as $title) {
            $poster = str_replace('posters/', 'posters/', $title->poster);
            $backdrop = str_replace('backdrops/', 'backdrops/', $title->backdrop);
            
            DB::table('titles')
                ->where('id', $title->id)
                ->update([
                    'poster' => $poster,
                    'backdrop' => $backdrop,
                ]);
        }
        
        // Update categories
        $categories = DB::table('categories')->get();
        
        foreach ($categories as $category) {
            $image = str_replace('images/categories/', 'categories/', $category->image);
            $heroImage = str_replace('images/heroes/', 'heroes/', $category->hero_image);
            
            DB::table('categories')
                ->where('id', $category->id)
                ->update([
                    'image' => $image,
                    'hero_image' => $heroImage,
                ]);
        }
        
        $this->command->info('Media paths updated to use storage system');
    }
}