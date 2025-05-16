<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class BatchImportDramas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dorasia:batch-import {--country=all : Country to import from (all, kr, jp, cn, th)}
                           {--total-pages=5 : Total number of pages to import}
                           {--batch-size=1 : Number of pages to import in a single batch}
                           {--truncate : Whether to truncate tables before import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import dramas in batches to avoid timeouts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $country = $this->option('country');
        $totalPages = (int) $this->option('total-pages');
        $batchSize = (int) $this->option('batch-size');
        $shouldTruncate = $this->option('truncate');
        
        if ($shouldTruncate) {
            $this->info('Truncating database tables before import...');
            Artisan::call('db:seed', [
                '--class' => 'TruncateImportedDataSeeder'
            ]);
            $this->info(Artisan::output());
        }
        
        $this->info("Starting batch import of {$totalPages} pages of {$country} dramas");
        $this->info("Using batch size of {$batchSize} pages");
        
        $successCount = 0;
        $progressBar = $this->output->createProgressBar($totalPages);
        $progressBar->start();
        
        for ($page = 1; $page <= $totalPages; $page += $batchSize) {
            $endPage = min($page + $batchSize - 1, $totalPages);
            $this->info("\nImporting pages {$page} to {$endPage}...");
            
            // Count existing records
            $beforeCount = DB::table('titles')->count();
            
            // Import each page separately
            for ($currentPage = $page; $currentPage <= $endPage; $currentPage++) {
                $this->comment("Processing page {$currentPage}...");
                
                Artisan::call('dorasia:import-romantic-dramas', [
                    '--country' => $country,
                    '--pages' => 1,
                    '--page' => $currentPage
                ]);
                
                // Extract the number of imported titles from the output
                $output = Artisan::output();
                $this->line(trim($output));
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
        
        $progressBar->finish();
        $this->newLine(2);
        $this->info("Batch import completed! Total imported: {$successCount} titles");
        
        return Command::SUCCESS;
    }
}