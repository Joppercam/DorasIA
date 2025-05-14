<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TripleImportDramas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dorasia:triple-import {--country=all : Country to import from (all, kr, jp, cn, th)}
                           {--truncate : Whether to truncate tables before import}
                           {--parallel=2 : Number of concurrent pages to process (1-3)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import triple the amount of dramas with improved performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $country = $this->option('country');
        $shouldTruncate = $this->option('truncate');
        $parallel = min(3, max(1, (int) $this->option('parallel')));
        
        // Set the total number of pages to import for each country - triple the default
        $totalPagesByCountry = [
            'all' => 15, // 5 x 3
            'kr' => 15,  // 5 x 3
            'jp' => 15,  // 5 x 3
            'cn' => 15,  // 5 x 3
            'th' => 15,  // 5 x 3
        ];
        
        $totalPages = $totalPagesByCountry[$country] ?? 15;
        
        if ($shouldTruncate) {
            $this->info('Truncating database tables before import...');
            Artisan::call('db:seed', [
                '--class' => 'TruncateImportedDataSeeder'
            ]);
            $this->info(Artisan::output());
        }
        
        $this->info("Starting enhanced import of {$totalPages} pages of {$country} dramas");
        $this->info("Using parallel processing with {$parallel} concurrent pages");
        
        $successCount = 0;
        $progressBar = $this->output->createProgressBar($totalPages);
        $progressBar->start();
        
        // Process in batches for better performance
        $batchSize = $parallel;
        
        for ($page = 1; $page <= $totalPages; $page += $batchSize) {
            $endPage = min($page + $batchSize - 1, $totalPages);
            $this->info("\nImporting pages {$page} to {$endPage}...");
            
            // Count existing records
            $beforeCount = DB::table('titles')->count();
            
            // Process pages in parallel when possible
            $pagesToProcess = range($page, $endPage);
            
            if ($parallel > 1) {
                // Parallel processing
                $this->processPagesBatch($pagesToProcess, $country);
            } else {
                // Sequential processing
                foreach ($pagesToProcess as $currentPage) {
                    $this->processPage($currentPage, $country);
                }
            }
            
            // Count how many new records were added
            $afterCount = DB::table('titles')->count();
            $newRecords = $afterCount - $beforeCount;
            $successCount += $newRecords;
            
            $this->info("Imported {$newRecords} new titles in this batch");
            $progressBar->advance($endPage - $page + 1);
            
            // Sleep between batches to avoid overwhelming the server
            if ($page + $batchSize <= $totalPages) {
                $this->comment("Pausing for 2 seconds before next batch...");
                sleep(2);
            }
        }
        
        // After importing all main pages, get additional recommendation data
        $this->info("\nEnriching data with recommendations...");
        $this->enrichWithRecommendations();
        
        $progressBar->finish();
        $this->newLine(2);
        $this->info("Enhanced import completed! Total imported: {$successCount} titles");
        $this->info("Final counts:");
        $this->info("- Titles: " . DB::table('titles')->count());
        $this->info("- Seasons: " . DB::table('seasons')->count());
        $this->info("- Episodes: " . DB::table('episodes')->count());
        $this->info("- People: " . DB::table('people')->count());
        
        return Command::SUCCESS;
    }
    
    /**
     * Process a batch of pages in parallel when possible
     * 
     * @param array $pages
     * @param string $country
     * @return void
     */
    protected function processPagesBatch(array $pages, string $country)
    {
        $commands = [];
        foreach ($pages as $page) {
            $commands[] = "php artisan dorasia:import-romantic-dramas --country={$country} --page={$page} --pages=1 > /dev/null 2>&1 &";
        }
        
        // Join commands and execute them in parallel
        $parallelCommand = implode(" ", $commands) . " wait";
        $this->comment("Executing parallel import for pages: " . implode(", ", $pages));
        
        // Execute the parallel commands
        $output = shell_exec($parallelCommand);
        
        // Give the database a moment to catch up
        sleep(1);
    }
    
    /**
     * Process a single page
     * 
     * @param int $page
     * @param string $country
     * @return void
     */
    protected function processPage(int $page, string $country)
    {
        $this->comment("Processing page {$page}...");
        
        Artisan::call('dorasia:import-romantic-dramas', [
            '--country' => $country,
            '--pages' => 1,
            '--page' => $page
        ]);
        
        // Extract the number of imported titles from the output
        $output = Artisan::output();
        $this->line(trim($output));
    }
    
    /**
     * Enrich the database with recommendations
     * 
     * @return void
     */
    protected function enrichWithRecommendations()
    {
        // Get a sample of existing titles to fetch recommendations for
        $titleIds = DB::table('titles')
            ->where('tmdb_id', '>', 0)
            ->inRandomOrder()
            ->limit(10)  // Just get 10 titles for recommendations to avoid overwhelming the API
            ->pluck('tmdb_id');
        
        if ($titleIds->isEmpty()) {
            $this->warn("No titles found to fetch recommendations for");
            return;
        }
        
        $this->info("Fetching recommendations for " . $titleIds->count() . " titles");
        
        foreach ($titleIds as $tmdbId) {
            $this->comment("Processing recommendations for TMDB ID: {$tmdbId}");
            
            Artisan::call('dorasia:import-recommendations', [
                '--tmdb-id' => $tmdbId,
                '--limit' => 5
            ]);
            
            $output = Artisan::output();
            Log::info("Recommendation import for TMDB ID {$tmdbId}: " . trim($output));
            
            // Sleep to avoid API rate limiting
            sleep(1);
        }
    }
}