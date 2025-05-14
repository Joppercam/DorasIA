<?php

namespace App\Console\Commands;

use App\Services\TmdbService;
use Illuminate\Console\Command;

class TestTmdbConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:tmdb-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the connection to the TMDB API';

    /**
     * @var TmdbService
     */
    protected $tmdbService;

    /**
     * Create a new command instance.
     */
    public function __construct(TmdbService $tmdbService)
    {
        parent::__construct();
        $this->tmdbService = $tmdbService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing TMDB API connection...');
        
        // Check API key configuration
        $this->info('TMDB API Key: ' . ($this->tmdbService->apiKey ? 'Configured' : 'Not configured'));
        // Skip access token check as it's a protected property
        
        // Test the connection by trying to search for a simple query
        try {
            $this->info('Attempting to search for "Parasite"...');
            $results = $this->tmdbService->searchShows('Parasite');
            
            if ($results->isEmpty()) {
                $this->warn('No results found, but connection was successful.');
            } else {
                $this->info('Connection successful! Found ' . $results->count() . ' results.');
                
                // Display first 3 results
                $this->info('First 3 results:');
                foreach ($results->take(3) as $index => $show) {
                    $this->info(($index + 1) . '. ' . $show['name'] . ' (' . substr($show['first_air_date'] ?? 'Unknown', 0, 4) . ')');
                }
            }
            
            // Try getting Korean dramas
            $this->info('Attempting to fetch Korean dramas...');
            $kdramas = $this->tmdbService->getKoreanDramas(1);
            
            if ($kdramas->isEmpty()) {
                $this->warn('No Korean dramas found, but connection was successful.');
            } else {
                $this->info('Connection successful! Found ' . $kdramas->count() . ' Korean dramas.');
                
                // Display first 3 results
                $this->info('First 3 Korean dramas:');
                foreach ($kdramas->take(3) as $index => $drama) {
                    $this->info(($index + 1) . '. ' . $drama['name'] . ' (' . substr($drama['first_air_date'] ?? 'Unknown', 0, 4) . ')');
                }
            }
            
            $this->info('TMDB API connection test completed successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('TMDB API connection failed: ' . $e->getMessage());
            return 1;
        }
    }
}