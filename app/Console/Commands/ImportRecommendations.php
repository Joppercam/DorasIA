<?php

namespace App\Console\Commands;

use App\Models\Title;
use App\Services\TmdbService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportRecommendations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dorasia:import-recommendations {--tmdb-id= : TMDB ID of the title to get recommendations for} 
                          {--limit=5 : Maximum number of recommendations to import}
                          {--skip-existing : Skip titles that already exist in the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import recommendations for a specific title';

    /**
     * The TMDB service instance.
     *
     * @var \App\Services\TmdbService
     */
    protected $tmdbService;

    /**
     * Create a new command instance.
     *
     * @return void
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
        $tmdbId = $this->option('tmdb-id');
        $limit = (int) $this->option('limit');
        
        if (empty($tmdbId)) {
            $this->error('TMDB ID is required');
            return Command::FAILURE;
        }
        
        $this->info("Fetching recommendations for TMDB ID: {$tmdbId}");
        
        try {
            // Get the title details including recommendations
            $details = $this->tmdbService->getShowDetails($tmdbId);
            
            if (empty($details) || empty($details['recommendations']['results'])) {
                $this->warn("No recommendations found for TMDB ID: {$tmdbId}");
                return Command::SUCCESS;
            }
            
            // Get the local title by TMDB ID
            $title = Title::where('tmdb_id', $tmdbId)->first();
            
            if (!$title) {
                $this->error("Title with TMDB ID {$tmdbId} not found in the database");
                return Command::FAILURE;
            }
            
            // Sort recommendations by popularity to get the most relevant ones
            $recommendations = collect($details['recommendations']['results'])
                ->sortByDesc('popularity')
                ->take($limit);
            
            $this->info("Found " . $recommendations->count() . " recommendations");
            
            $importCount = 0;
            
            // Import recommendations using ImportRomanticAsianDramas command
            foreach ($recommendations as $recommendation) {
                // Import the drama if not already in the database
                $existingTitle = Title::where('tmdb_id', $recommendation['id'])->first();
                
                if (!$existingTitle) {
                    $this->comment("Importing recommendation: " . $recommendation['name']);
                    
                    // Use the ImportRomanticAsianDramas command's importDrama method
                    $importCommand = app(ImportRomanticAsianDramas::class);
                    
                    // Pass skip-existing flag if it was provided
                    if ($this->option('skip-existing')) {
                        $importCommand->getApplication()->getArtisan()->run(
                            new \Symfony\Component\Console\Input\ArrayInput([
                                'command' => 'dorasia:import-romantic-dramas',
                                '--skip-existing' => true
                            ]),
                            new \Symfony\Component\Console\Output\NullOutput()
                        );
                    }
                    
                    if ($importCommand->importDrama($recommendation)) {
                        $importCount++;
                    }
                } else {
                    $this->line("Recommendation already exists: " . $recommendation['name']);
                }
            }
            
            $this->info("Successfully imported {$importCount} new recommendations for '{$title->title}'");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to import recommendations: " . $e->getMessage());
            Log::error("Recommendation import error: " . $e->getMessage(), [
                'tmdb_id' => $tmdbId,
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }
}