<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Series;
use App\Models\Person;
use App\Services\TmdbService;
use App\Services\TranslationService;
use Illuminate\Support\Facades\DB;

class ImportMissingActors extends Command
{
    protected $signature = 'tmdb:import-missing-actors {--limit=50 : Limit number of series to process}';
    protected $description = 'Import actors for series that do not have cast members';

    private $tmdbService;
    private $translationService;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->tmdbService = app(TmdbService::class);
        $this->translationService = app(TranslationService::class);
        
        $limit = $this->option('limit');
        
        // Get series without cast
        $seriesWithoutCast = Series::whereDoesntHave('people')
            ->whereNotNull('tmdb_id')
            ->limit($limit)
            ->get();
        
        if ($seriesWithoutCast->isEmpty()) {
            $this->info("All series already have cast members!");
            return 0;
        }

        $this->info("Found {$seriesWithoutCast->count()} series without cast members. Processing...");
        
        $bar = $this->output->createProgressBar($seriesWithoutCast->count());
        $bar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($seriesWithoutCast as $series) {
            try {
                $this->importActorsForSeries($series);
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $this->newLine();
                $this->error("Error with {$series->title}: " . $e->getMessage());
            }
            
            $bar->advance();
            
            // Small delay to avoid rate limiting
            usleep(250000); // 250ms delay
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Import completed!");
        $this->info("   Success: {$successCount}");
        $this->info("   Errors: {$errorCount}");
        
        // Show final statistics
        $totalSeriesWithCast = Series::whereHas('people')->count();
        $totalSeries = Series::count();
        $coverage = round(($totalSeriesWithCast / $totalSeries) * 100, 1);
        
        $this->info("📊 Cast Coverage:");
        $this->info("   Series with cast: {$totalSeriesWithCast}/{$totalSeries} ({$coverage}%)");

        return 0;
    }

    private function importActorsForSeries($series)
    {
        if (!$series->tmdb_id) {
            throw new \Exception("No TMDB ID");
        }

        // Get cast data from TMDB
        $credits = $this->tmdbService->getSeriesCredits($series->tmdb_id);
        
        if (!isset($credits['cast']) || empty($credits['cast'])) {
            return;
        }

        $castCount = 0;
        foreach ($credits['cast'] as $castMember) {
            if ($castCount >= 15) break; // Limit to main cast
            
            // Import or update actor
            $actor = $this->importActor($castMember['id']);
            
            if ($actor) {
                // Create relationship with series
                $series->people()->syncWithoutDetaching([
                    $actor->id => [
                        'character' => $castMember['character'] ?? null,
                        'order' => $castMember['order'] ?? 999,
                        'department' => 'Acting',
                        'job' => 'Actor',
                        'role' => 'cast'
                    ]
                ]);
                $castCount++;
            }
        }

        // Import main crew (directors, writers) - limited
        if (isset($credits['crew'])) {
            $crewCount = 0;
            foreach ($credits['crew'] as $crewMember) {
                if ($crewCount >= 5) break; // Limit crew members
                
                if (in_array($crewMember['job'], ['Director', 'Writer', 'Executive Producer'])) {
                    $person = $this->importActor($crewMember['id']);
                    
                    if ($person) {
                        $series->people()->syncWithoutDetaching([
                            $person->id => [
                                'department' => $crewMember['department'] ?? 'Production',
                                'job' => $crewMember['job'] ?? 'Crew',
                                'role' => 'crew'
                            ]
                        ]);
                        $crewCount++;
                    }
                }
            }
        }
    }

    private function importActor($tmdbId)
    {
        try {
            // Check if actor already exists
            $existingActor = Person::where('tmdb_id', $tmdbId)->first();
            
            if ($existingActor) {
                return $existingActor;
            }
            
            // Get detailed actor info from TMDB
            $actorData = $this->tmdbService->getPersonDetails($tmdbId);
            
            if (!$actorData) {
                return null;
            }

            // Basic translation for biography if available and not too long
            $biographyEs = null;
            if ($actorData['biography'] && strlen($actorData['biography']) < 500 && config('services.openai.api_key')) {
                try {
                    $biographyEs = $this->translationService->translateToChileanSpanish($actorData['biography'], 'actor');
                } catch (\Exception $e) {
                    // Silent fail for translation
                }
            }

            $data = [
                'name' => $actorData['name'],
                'known_for_department' => $actorData['known_for_department'] ?? 'Acting',
                'biography' => $actorData['biography'] ?? null,
                'biography_es' => $biographyEs,
                'birthday' => $actorData['birthday'] ?? null,
                'deathday' => $actorData['deathday'] ?? null,
                'place_of_birth' => $actorData['place_of_birth'] ?? null,
                'profile_path' => $actorData['profile_path'] ?? null,
                'imdb_id' => $actorData['imdb_id'] ?? null,
                'tmdb_id' => $tmdbId,
                'popularity' => $actorData['popularity'] ?? 0,
                'adult' => $actorData['adult'] ?? false,
                'gender' => $actorData['gender'] ?? 0
            ];

            return Person::create($data);

        } catch (\Exception $e) {
            throw new \Exception("Error importing actor {$tmdbId}: " . $e->getMessage());
        }
    }
}