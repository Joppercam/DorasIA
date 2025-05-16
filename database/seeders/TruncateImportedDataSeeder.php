<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateImportedDataSeeder extends Seeder
{
    /**
     * Truncate all imported data tables.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow truncating tables with relationships
        DB::statement('PRAGMA foreign_keys = OFF');
        
        // Truncate pivot tables first
        if (Schema::hasTable('title_person')) {
            $this->command->info('Truncating title_person table...');
            DB::table('title_person')->truncate();
        }
        
        if (Schema::hasTable('title_genre')) {
            $this->command->info('Truncating title_genre table...');
            DB::table('title_genre')->truncate();
        }
        
        // Now truncate main content tables
        $tables = [
            'episodes',
            'seasons',
            'titles',
            'people',
        ];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $this->command->info("Truncating {$table} table...");
                DB::table($table)->truncate();
            }
        }
        
        // Re-enable foreign key checks
        DB::statement('PRAGMA foreign_keys = ON');
        
        $this->command->info('Successfully truncated all import tables!');
    }
}