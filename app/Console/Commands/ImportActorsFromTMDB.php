<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Series;
use App\Models\Person;
use App\Services\TmdbService;
use App\Services\TranslationService;
use Illuminate\Support\Facades\DB;

class ImportActorsFromTMDB extends Command
{
    protected $signature = 'tmdb:import-actors {--series=all : Series ID or "all" for all series}';
    protected $description = 'Import actors data from TMDB for series';

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
        
        $seriesOption = $this->option('series');
        
        if ($seriesOption === 'all') {
            $series = Series::whereNotNull('tmdb_id')->get();
            $this->info("Importing actors for all series ({$series->count()} series)...");
        } else {
            $series = Series::where('id', $seriesOption)->orWhere('tmdb_id', $seriesOption)->get();
            if ($series->isEmpty()) {
                $this->error("Series not found!");
                return 1;
            }
        }

        $bar = $this->output->createProgressBar($series->count());
        $bar->start();

        foreach ($series as $serie) {
            $this->importActorsForSeries($serie);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Actors import completed!");
        
        // Show statistics
        $totalActors = Person::count();
        $actorsWithBio = Person::whereNotNull('biography')->count();
        $koreanActors = Person::where('place_of_birth', 'LIKE', '%Korea%')->count();
        
        $this->info("📊 Statistics:");
        $this->info("   Total actors: {$totalActors}");
        $this->info("   Actors with biography: {$actorsWithBio}");
        $this->info("   Korean actors: {$koreanActors}");

        return 0;
    }

    private function importActorsForSeries($series)
    {
        if (!$series->tmdb_id) {
            $this->warn("Series {$series->title} has no TMDB ID");
            return;
        }

        try {
            // Get cast data from TMDB
            $credits = $this->tmdbService->getSeriesCredits($series->tmdb_id);
            
            if (!isset($credits['cast'])) {
                return;
            }

            foreach ($credits['cast'] as $castMember) {
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
                }
            }

            // Also import main crew (directors, writers)
            if (isset($credits['crew'])) {
                foreach ($credits['crew'] as $crewMember) {
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
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            $this->error("Error importing actors for {$series->title}: " . $e->getMessage());
        }
    }

    private function importActor($tmdbId)
    {
        try {
            // Check if actor already exists
            $existingActor = Person::where('tmdb_id', $tmdbId)->first();
            
            // Get detailed actor info from TMDB
            $actorData = $this->tmdbService->getPersonDetails($tmdbId);
            
            if (!$actorData) {
                return null;
            }

            // Get additional data (combined credits, images)
            $credits = $this->tmdbService->getPersonCombinedCredits($tmdbId);
            $images = $this->tmdbService->getPersonImages($tmdbId);
            
            // Calculate popularity based on known works
            $popularity = $actorData['popularity'] ?? 0;
            if ($credits && isset($credits['cast'])) {
                $popularity = max($popularity, count($credits['cast']) * 0.5);
            }

            // Translate biography and place of birth if available
            $biographyEs = null;
            $placeOfBirthEs = null;
            
            if ($actorData['biography'] && config('services.openai.api_key')) {
                try {
                    $biographyEs = $this->translationService->translateToChileanSpanish($actorData['biography'], 'actor');
                } catch (\Exception $e) {
                    // Silent fail for translation
                }
            }
            
            if ($actorData['place_of_birth'] && config('services.openai.api_key')) {
                try {
                    $placeOfBirthEs = $this->translationService->translateToChileanSpanish($actorData['place_of_birth'], 'actor');
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
                'place_of_birth_es' => $placeOfBirthEs,
                'profile_path' => $actorData['profile_path'] ?? null,
                'imdb_id' => $actorData['imdb_id'] ?? null,
                'tmdb_id' => $tmdbId,
                'popularity' => $popularity,
                'adult' => $actorData['adult'] ?? false,
                'homepage' => $actorData['homepage'] ?? null,
                'also_known_as' => $actorData['also_known_as'] ?? [],
                'gender' => $actorData['gender'] ?? 0
            ];

            if ($existingActor) {
                $existingActor->update($data);
                return $existingActor;
            } else {
                return Person::create($data);
            }

        } catch (\Exception $e) {
            $this->error("Error importing actor {$tmdbId}: " . $e->getMessage());
            return null;
        }
    }
}