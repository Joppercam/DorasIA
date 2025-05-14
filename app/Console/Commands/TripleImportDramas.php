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
                           {--preserve : Keep existing data and only add new titles}
                           {--pages=15 : Number of pages to import (default 15)}
                           {--parallel=2 : Number of concurrent pages to process (1-3)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import large amounts of dramas with improved performance while preserving existing data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $country = $this->option('country');
        $shouldTruncate = $this->option('truncate');
        $shouldPreserve = $this->option('preserve');
        $parallel = min(3, max(1, (int) $this->option('parallel')));
        $requestedPages = (int) $this->option('pages');
        
        // Set the total number of pages to import for each country - configurable via command line
        $totalPagesByCountry = [
            'all' => $requestedPages, 
            'kr' => $requestedPages,  
            'jp' => $requestedPages,  
            'cn' => $requestedPages,  
            'th' => $requestedPages,  
        ];
        
        $totalPages = $totalPagesByCountry[$country] ?? $requestedPages;
        
        // Display warning about importing a large number of pages
        if ($totalPages > 30) {
            $this->warn("You're about to import {$totalPages} pages of data. This might take a long time and use a lot of API requests.");
            if (!$this->confirm('Are you sure you want to continue?', true)) {
                $this->info('Operation canceled.');
                return Command::SUCCESS;
            }
        }
        
        // Handle preserve/truncate options
        if ($shouldTruncate && $shouldPreserve) {
            $this->error('Cannot use both --truncate and --preserve options together. Please choose one.');
            return Command::FAILURE;
        }
        
        if ($shouldTruncate) {
            $this->info('Truncating database tables before import...');
            if ($this->confirm('This will delete ALL existing data. Are you sure?', false)) {
                Artisan::call('db:seed', [
                    '--class' => 'TruncateImportedDataSeeder'
                ]);
                $this->info(Artisan::output());
            } else {
                $this->info('Truncation canceled. Continuing with import without truncating.');
            }
        }
        
        $this->info("Starting enhanced import of {$totalPages} pages of {$country} dramas");
        if ($shouldPreserve) {
            $this->info("Using preserve mode - only new titles will be added");
        }
        $this->info("Using parallel processing with {$parallel} concurrent pages");
        
        // Get existing title count for reference
        $existingCount = DB::table('titles')->count();
        $this->info("Current database has {$existingCount} titles");
        
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
        
        // Calculate final statistics
        $finalTitleCount = DB::table('titles')->count();
        $newTitlesCount = $finalTitleCount - $existingCount;
        
        $this->info("Enhanced import completed!");
        $this->info("- Started with: {$existingCount} titles");
        $this->info("- New titles added: {$newTitlesCount}");
        $this->info("- Total titles now: {$finalTitleCount}");
        $this->info("\nFinal database statistics:");
        $this->info("- Titles: " . $finalTitleCount);
        $this->info("- Seasons: " . DB::table('seasons')->count());
        $this->info("- Episodes: " . DB::table('episodes')->count());
        $this->info("- People: " . DB::table('people')->count());
        
        if ($newTitlesCount > 0) {
            $this->info("\nImport successful! Your catalog now has more content.");
        } else {
            $this->warn("\nNo new titles were added. You may want to try different settings or import from different countries.");
        }
        
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
        
        $importArgs = [
            '--country' => $country,
            '--pages' => 1,
            '--page' => $page
        ];
        
        // Add skip-existing flag if preserve mode is enabled
        if ($this->option('preserve')) {
            $importArgs['--skip-existing'] = true;
        }
        
        Artisan::call('dorasia:import-romantic-dramas', $importArgs);
        
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
            ->limit(20)  // Get 20 titles for more variety in recommendations
            ->pluck('tmdb_id');
        
        if ($titleIds->isEmpty()) {
            $this->warn("No titles found to fetch recommendations for");
            return;
        }
        
        $this->info("Fetching recommendations for " . $titleIds->count() . " titles");
        
        foreach ($titleIds as $tmdbId) {
            $this->comment("Processing recommendations for TMDB ID: {$tmdbId}");
            
            $recommendationArgs = [
                '--tmdb-id' => $tmdbId,
                '--limit' => 10  // Increased from 5 to 10 for more content
            ];
            
            // Add skip-existing flag if preserve mode is enabled
            if ($this->option('preserve')) {
                $recommendationArgs['--skip-existing'] = true;
            }
            
            Artisan::call('dorasia:import-recommendations', $recommendationArgs);
            
            $output = Artisan::output();
            Log::info("Recommendation import for TMDB ID {$tmdbId}: " . trim($output));
            
            // Sleep to avoid API rate limiting
            sleep(1);
        }
    }
}