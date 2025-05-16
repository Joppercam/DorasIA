<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class FixPersonTable extends Command
{
    protected $signature = 'dorasia:fix-person-table';
    protected $description = 'Fix the Person table structure for TMDB integration';

    public function handle()
    {
        $this->info('Checking Person table...');
        
        if (Schema::hasTable('people')) {
            // Check for profile_path column
            if (!Schema::hasColumn('people', 'profile_path')) {
                $this->warn("Adding profile_path column to people table...");
                Schema::table('people', function (Blueprint $table) {
                    $table->string('profile_path')->nullable();
                });
            }
            
            // Check for tmdb_id column
            if (!Schema::hasColumn('people', 'tmdb_id')) {
                $this->warn("Adding tmdb_id column to people table...");
                Schema::table('people', function (Blueprint $table) {
                    $table->unsignedBigInteger('tmdb_id')->nullable();
                });
            }
            
            // Check other fields that might be needed
            $columns = ['biography', 'birth_date', 'death_date', 'place_of_birth', 'gender', 'popularity', 'slug'];
            
            foreach ($columns as $column) {
                if (!Schema::hasColumn('people', $column)) {
                    $this->warn("Adding $column column to people table...");
                    
                    Schema::table('people', function (Blueprint $table) use ($column) {
                        if ($column === 'biography') {
                            $table->text('biography')->nullable();
                        } elseif ($column === 'birth_date' || $column === 'death_date') {
                            $table->date($column)->nullable();
                        } elseif ($column === 'place_of_birth') {
                            $table->string('place_of_birth')->nullable();
                        } elseif ($column === 'gender') {
                            $table->tinyInteger('gender')->nullable();
                        } elseif ($column === 'popularity') {
                            $table->float('popularity')->default(0);
                        } elseif ($column === 'slug') {
                            $table->string('slug')->nullable();
                        }
                    });
                }
            }
            
            $this->info('Person table structure checked and fixed if needed.');
        } else {
            $this->error('Person table does not exist!');
        }
        
        return Command::SUCCESS;
    }
}