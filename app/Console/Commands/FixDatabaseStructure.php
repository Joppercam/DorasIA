<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

class FixDatabaseStructure extends Command
{
    protected $signature = 'dorasia:fix-database';
    protected $description = 'Fix database structure to ensure it matches model expectations';

    public function handle()
    {
        $this->info('Checking database structure...');
        
        // Fix Episode table
        $this->fixEpisodeTable();
        
        // Fix Season table
        $this->fixSeasonTable();
        
        // Fix Title table
        $this->fixTitleTable();
        
        $this->info('Database structure check and fixes completed.');
        
        return Command::SUCCESS;
    }
    
    private function fixEpisodeTable()
    {
        $this->info('Checking Episodes table...');
        
        if (Schema::hasTable('episodes')) {
            if (!Schema::hasColumn('episodes', 'title_id')) {
                $this->warn('Adding title_id column to episodes table...');
                Schema::table('episodes', function (Blueprint $table) {
                    $table->foreignId('title_id')->nullable()->after('id');
                });
            }
            
            // Check if we need to rename episode_number to number
            if (Schema::hasColumn('episodes', 'episode_number') && !Schema::hasColumn('episodes', 'number')) {
                $this->warn('Renaming episode_number to number in episodes table...');
                Schema::table('episodes', function (Blueprint $table) {
                    $table->renameColumn('episode_number', 'number');
                });
            }
            
            // Check for other necessary columns
            $columns = ['name', 'overview', 'still', 'tmdb_id'];
            
            foreach ($columns as $column) {
                if (!Schema::hasColumn('episodes', $column)) {
                    $this->warn("Adding $column column to episodes table...");
                    
                    Schema::table('episodes', function (Blueprint $table) use ($column) {
                        if ($column === 'name') {
                            $table->string('name')->nullable();
                        } elseif ($column === 'overview') {
                            $table->text('overview')->nullable();
                        } elseif ($column === 'still') {
                            $table->string('still')->nullable(); 
                        } elseif ($column === 'tmdb_id') {
                            $table->unsignedBigInteger('tmdb_id')->nullable();
                        }
                    });
                }
            }
            
            $this->info('Episodes table structure checked and fixed if needed.');
        } else {
            $this->error('Episodes table does not exist!');
        }
    }
    
    private function fixSeasonTable()
    {
        $this->info('Checking Seasons table...');
        
        if (Schema::hasTable('seasons')) {
            // Check if we need to rename season_number to number
            if (Schema::hasColumn('seasons', 'season_number') && !Schema::hasColumn('seasons', 'number')) {
                $this->warn('Renaming season_number to number in seasons table...');
                Schema::table('seasons', function (Blueprint $table) {
                    $table->renameColumn('season_number', 'number');
                });
            }
            
            // Check for necessary columns
            $columns = ['name', 'overview', 'poster', 'tmdb_id', 'air_date'];
            
            foreach ($columns as $column) {
                if (!Schema::hasColumn('seasons', $column)) {
                    $this->warn("Adding $column column to seasons table...");
                    
                    Schema::table('seasons', function (Blueprint $table) use ($column) {
                        if ($column === 'name') {
                            $table->string('name')->nullable();
                        } elseif ($column === 'overview') {
                            $table->text('overview')->nullable();
                        } elseif ($column === 'poster') {
                            $table->string('poster')->nullable();
                        } elseif ($column === 'tmdb_id') {
                            $table->unsignedBigInteger('tmdb_id')->nullable();
                        } elseif ($column === 'air_date') {
                            $table->date('air_date')->nullable();
                        }
                    });
                }
            }
            
            $this->info('Seasons table structure checked and fixed if needed.');
        } else {
            $this->error('Seasons table does not exist!');
        }
    }
    
    private function fixTitleTable()
    {
        $this->info('Checking Titles table...');
        
        if (Schema::hasTable('titles')) {
            // Check for necessary columns for TMDB integration
            $columns = ['tmdb_id', 'poster_path', 'backdrop_path', 'vote_average', 'vote_count', 'popularity', 'metadata', 'streaming_platforms', 'description', 'overview', 'release_date', 'runtime', 'status', 'type', 'is_featured', 'is_trending'];
            
            foreach ($columns as $column) {
                if (!Schema::hasColumn('titles', $column)) {
                    $this->warn("Adding $column column to titles table...");
                    
                    Schema::table('titles', function (Blueprint $table) use ($column) {
                        if ($column === 'tmdb_id') {
                            $table->unsignedBigInteger('tmdb_id')->nullable();
                        } elseif ($column === 'poster_path' || $column === 'backdrop_path') {
                            $table->string($column)->nullable();
                        } elseif ($column === 'vote_average' || $column === 'popularity') {
                            $table->float($column, 8, 3)->default(0);
                        } elseif ($column === 'vote_count') {
                            $table->integer($column)->default(0);
                        } elseif ($column === 'metadata' || $column === 'streaming_platforms') {
                            $table->json($column)->nullable();
                        } elseif ($column === 'description' || $column === 'overview') {
                            $table->text($column)->nullable();
                        } elseif ($column === 'release_date') {
                            $table->date($column)->nullable();
                        } elseif ($column === 'runtime') {
                            $table->integer($column)->nullable();
                        } elseif ($column === 'status') {
                            $table->string($column)->nullable();
                        } elseif ($column === 'type') {
                            $table->string($column)->default('series');
                        } elseif ($column === 'is_featured' || $column === 'is_trending') {
                            $table->boolean($column)->default(false);
                        }
                    });
                }
            }
            
            // If the title has overview but not description, let's make them aliases
            if (Schema::hasColumn('titles', 'overview') && !Schema::hasColumn('titles', 'description')) {
                $this->warn("Adding description column as alias for overview...");
                Schema::table('titles', function (Blueprint $table) {
                    $table->text('description')->nullable();
                });
            }
            
            // If the title has description but not overview, let's make them aliases
            if (Schema::hasColumn('titles', 'description') && !Schema::hasColumn('titles', 'overview')) {
                $this->warn("Adding overview column as alias for description...");
                Schema::table('titles', function (Blueprint $table) {
                    $table->text('overview')->nullable();
                });
            }
            
            $this->info('Titles table structure checked and fixed if needed.');
        } else {
            $this->error('Titles table does not exist!');
        }
    }
}