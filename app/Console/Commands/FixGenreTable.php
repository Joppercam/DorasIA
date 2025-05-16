<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FixGenreTable extends Command
{
    protected $signature = 'dorasia:fix-genre-table';
    protected $description = 'Fix the Genre table structure and data';

    public function handle()
    {
        $this->info('Checking Genre table...');
        
        if (Schema::hasTable('genres')) {
            // Check if slug column exists
            if (!Schema::hasColumn('genres', 'slug')) {
                $this->warn("Adding slug column to genres table...");
                Schema::table('genres', function (Blueprint $table) {
                    $table->string('slug')->nullable();
                });
                
                // Update existing records to add a slug based on name
                $this->info("Updating existing genre records with slugs...");
                $genres = DB::table('genres')->get();
                
                foreach ($genres as $genre) {
                    if (empty($genre->slug)) {
                        DB::table('genres')->where('id', $genre->id)->update([
                            'slug' => Str::slug($genre->name)
                        ]);
                    }
                }
                
                // Make slug required after updating all existing records
                Schema::table('genres', function (Blueprint $table) {
                    $table->string('slug')->nullable(false)->change();
                });
            }
            
            // Check for tmdb_id column
            if (!Schema::hasColumn('genres', 'tmdb_id')) {
                $this->warn("Adding tmdb_id column to genres table...");
                Schema::table('genres', function (Blueprint $table) {
                    $table->unsignedBigInteger('tmdb_id')->nullable();
                });
            }
            
            // Modify the name field to accept the latest TMDB genre names
            $genreMapping = [
                'Action & Adventure' => ['name' => 'Action & Adventure', 'slug' => 'action-adventure', 'tmdb_id' => 10759],
                'Animación' => ['name' => 'Animación', 'slug' => 'animacion', 'tmdb_id' => 16],
                'War & Politics' => ['name' => 'War & Politics', 'slug' => 'war-politics', 'tmdb_id' => 10768],
                'Soap' => ['name' => 'Soap', 'slug' => 'soap', 'tmdb_id' => 10766],
                'Misterio' => ['name' => 'Misterio', 'slug' => 'misterio', 'tmdb_id' => 9648],
                'Familia' => ['name' => 'Familia', 'slug' => 'familia', 'tmdb_id' => 10751],
                'Comedia' => ['name' => 'Comedia', 'slug' => 'comedia', 'tmdb_id' => 35],
                'Drama' => ['name' => 'Drama', 'slug' => 'drama', 'tmdb_id' => 18],
                'Romance' => ['name' => 'Romance', 'slug' => 'romance', 'tmdb_id' => 10749],
            ];
            
            // Seed missing genres
            $this->info("Seeding genre mapping for TMDB genres...");
            foreach ($genreMapping as $genreName => $genreData) {
                // Check if genre exists
                $exists = DB::table('genres')->where('name', $genreName)->exists();
                
                if (!$exists) {
                    $this->info("Creating genre: {$genreName}");
                    DB::table('genres')->insert([
                        'name' => $genreData['name'],
                        'slug' => $genreData['slug'],
                        'tmdb_id' => $genreData['tmdb_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            
            $this->info('Genre table structure checked and fixed if needed.');
        } else {
            $this->error('Genre table does not exist!');
        }
        
        return Command::SUCCESS;
    }
}