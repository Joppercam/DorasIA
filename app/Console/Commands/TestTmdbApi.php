<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class TestTmdbApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dorasia:test-tmdb-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the TMDB API connection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing TMDB API connection...');
        
        $apiKey = env('TMDB_API_KEY', '69e4c68f7f83d0388b2f66bfaca96337');
        
        // Create a new client without base URL to ensure full URL is used
        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
            ],
            'timeout' => 10,
        ]);
        
        try {
            // Test a simple API endpoint with the full URL
            $url = "https://api.themoviedb.org/3/movie/550?api_key={$apiKey}";
            $this->info("Testing URL: " . $url);
            
            $response = $client->request('GET', $url);
            $data = json_decode($response->getBody()->getContents(), true);
            
            $this->info("Success! Got response for movie: " . ($data['title'] ?? 'Unknown'));
            $this->info("Now testing discover endpoint...");
            
            // Test the discover endpoint
            $url = "https://api.themoviedb.org/3/discover/tv?api_key={$apiKey}&with_genres=18|10749&language=es-ES&page=1";
            $this->info("Testing URL: " . $url);
            
            $response = $client->request('GET', $url);
            $data = json_decode($response->getBody()->getContents(), true);
            
            $this->info("Success! Discover endpoint returned " . ($data['total_results'] ?? 0) . " results");
            
            // Test with Asian countries filter
            $url = "https://api.themoviedb.org/3/discover/tv?api_key={$apiKey}&with_genres=18|10749&with_origin_country=KR|JP|CN|TW&language=es-ES&page=1";
            $this->info("Testing URL with origin countries: " . $url);
            
            $response = $client->request('GET', $url);
            $data = json_decode($response->getBody()->getContents(), true);
            
            $this->info("Success! Asian discover endpoint returned " . ($data['total_results'] ?? 0) . " results");
            $this->info("First result: " . ($data['results'][0]['name'] ?? 'None'));
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("API test failed: " . $e->getMessage());
            Log::error("TMDB API Test Error: " . $e->getMessage());
            
            if ($e->hasResponse()) {
                $this->error("Response status: " . $e->getResponse()->getStatusCode());
                $this->error("Response body: " . substr($e->getResponse()->getBody()->getContents(), 0, 500));
            }
            
            return Command::FAILURE;
        }
    }
}