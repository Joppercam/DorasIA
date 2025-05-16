<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Person;

class FixPeopleSlugCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dorasia:fix-people-slug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix missing slug values in people table and ensure proper slug generation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking people table structure...');

        // Check if slug exists in the people table
        if (!Schema::hasColumn('people', 'slug')) {
            $this->info('Adding slug column to people table...');
            Schema::table('people', function($table) {
                $table->string('slug')->nullable();
            });
        }

        $this->info('Updating people records with missing slugs...');
        
        // Get all people with missing slugs
        $peopleWithoutSlugs = Person::whereNull('slug')->orWhere('slug', '')->get();
        $total = $peopleWithoutSlugs->count();
        
        $this->info("Found {$total} records with missing slugs");
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        foreach ($peopleWithoutSlugs as $person) {
            // Generate slug from name if available
            if (!empty($person->name)) {
                $person->slug = Str::slug($person->name);
            } 
            // Use TMDB ID as fallback
            else if (!empty($person->tmdb_id)) {
                $person->slug = 'person-' . $person->tmdb_id;
            } 
            // Use DB ID as last resort
            else {
                $person->slug = 'person-' . $person->id;
            }
            
            $person->save();
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info('People slug fix completed successfully!');
        
        return Command::SUCCESS;
    }
}