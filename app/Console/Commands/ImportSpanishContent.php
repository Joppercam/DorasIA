<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TmdbService;
use App\Models\Series;
use App\Models\Person;
use App\Models\Genre;
use Illuminate\Support\Facades\DB;

class ImportSpanishContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dorasia:import-spanish {--series} {--actors} {--pages=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Korean content with Spanish translations from TMDB';

    private TmdbService $tmdb;

    public function __construct(TmdbService $tmdbService)
    {
        parent::__construct();
        $this->tmdb = $tmdbService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎬 Iniciando importación de contenido en español...');
        
        $pages = (int) $this->option('pages');
        
        if ($this->option('series')) {
            $this->importSeries($pages);
        }
        
        if ($this->option('actors')) {
            $this->importActors();
        }
        
        if (!$this->option('series') && !$this->option('actors')) {
            $this->importSeries($pages);
            $this->importActors();
        }
        
        $this->info('✅ Importación completada!');
    }

    private function importSeries(int $pages = 10)
    {
        $this->info('📺 Importando series K-Drama en español...');
        
        $progressBar = $this->output->createProgressBar($pages);
        $progressBar->start();
        
        for ($page = 1; $page <= $pages; $page++) {
            $data = $this->tmdb->getPopularKoreanDramas($page);
            
            if (!isset($data['results'])) {
                continue;
            }
            
            foreach ($data['results'] as $seriesData) {
                $this->importSingleSeries($seriesData);
            }
            
            $progressBar->advance();
            usleep(100000); // 100ms delay to avoid rate limiting
        }
        
        $progressBar->finish();
        $this->line('');
        $this->info('📺 Series importadas exitosamente');
    }

    private function importSingleSeries(array $seriesData)
    {
        try {
            // Get detailed series information
            $detailedData = $this->tmdb->getTvShowDetails($seriesData['id']);
            
            if (empty($detailedData)) {
                return;
            }
            
            // Create or update series
            $series = Series::updateOrCreate(
                ['tmdb_id' => $seriesData['id']],
                [
                    'title' => $seriesData['original_name'] ?? $seriesData['name'] ?? '',
                    'title_es' => $detailedData['name'] ?? $seriesData['name'] ?? '',
                    'original_title' => $seriesData['original_name'] ?? '',
                    'overview' => $seriesData['overview'] ?? '',
                    'overview_es' => $detailedData['overview'] ?? $seriesData['overview'] ?? '',
                    'poster_path' => $seriesData['poster_path'] ?? null,
                    'backdrop_path' => $seriesData['backdrop_path'] ?? null,
                    'first_air_date' => $seriesData['first_air_date'] ? date('Y-m-d', strtotime($seriesData['first_air_date'])) : null,
                    'last_air_date' => $detailedData['last_air_date'] ? date('Y-m-d', strtotime($detailedData['last_air_date'])) : null,
                    'vote_average' => $seriesData['vote_average'] ?? 0,
                    'vote_count' => $seriesData['vote_count'] ?? 0,
                    'popularity' => $seriesData['popularity'] ?? 0,
                    'number_of_episodes' => $detailedData['number_of_episodes'] ?? 0,
                    'number_of_seasons' => $detailedData['number_of_seasons'] ?? 0,
                    'status' => $detailedData['status'] ?? 'Unknown',
                    'type' => $detailedData['type'] ?? 'Scripted',
                    'in_production' => $detailedData['in_production'] ?? false,
                    'homepage' => $detailedData['homepage'] ?? null,
                    'tagline' => $detailedData['tagline'] ?? null,
                    'tagline_es' => $detailedData['tagline'] ?? null,
                    'is_korean_drama' => true,
                ]
            );
            
            // Import genres
            if (isset($detailedData['genres']) && is_array($detailedData['genres'])) {
                $genreIds = [];
                foreach ($detailedData['genres'] as $genreData) {
                    $genre = Genre::updateOrCreate(
                        ['tmdb_id' => $genreData['id']],
                        [
                            'name' => $genreData['name'],
                            'display_name' => $genreData['name']
                        ]
                    );
                    $genreIds[] = $genre->id;
                }
                $series->genres()->sync($genreIds);
            }
            
            // Import cast
            if (isset($detailedData['credits']['cast']) && is_array($detailedData['credits']['cast'])) {
                $this->importCast($series, $detailedData['credits']['cast']);
            }
            
        } catch (\Exception $e) {
            $this->error("Error importando serie ID {$seriesData['id']}: " . $e->getMessage());
        }
    }

    private function importCast(Series $series, array $castData)
    {
        $castIds = [];
        
        foreach (array_slice($castData, 0, 10) as $castMember) { // Import top 10 cast members
            try {
                $personData = $this->tmdb->getPersonDetails($castMember['id']);
                
                if (empty($personData)) {
                    continue;
                }
                
                $person = Person::updateOrCreate(
                    ['tmdb_id' => $castMember['id']],
                    [
                        'name' => $personData['name'] ?? $castMember['name'],
                        'display_name' => $personData['name'] ?? $castMember['name'],
                        'profile_path' => $personData['profile_path'] ?? $castMember['profile_path'],
                        'birthday' => $personData['birthday'] ? date('Y-m-d', strtotime($personData['birthday'])) : null,
                        'deathday' => $personData['deathday'] ? date('Y-m-d', strtotime($personData['deathday'])) : null,
                        'place_of_birth' => $personData['place_of_birth'] ?? null,
                        'biography' => $personData['biography'] ?? null,
                        'popularity' => $personData['popularity'] ?? 0,
                        'adult' => $personData['adult'] ?? false,
                        'gender' => $personData['gender'] ?? 0,
                        'known_for_department' => $personData['known_for_department'] ?? 'Acting',
                        'homepage' => $personData['homepage'] ?? null,
                        'imdb_id' => $personData['imdb_id'] ?? null,
                    ]
                );
                
                $castIds[$person->id] = [
                    'role' => 'actor',
                    'character' => $castMember['character'] ?? '',
                    'order' => $castMember['order'] ?? 999,
                    'department' => 'Acting',
                    'job' => 'Acting'
                ];
                
                usleep(50000); // 50ms delay between person imports
                
            } catch (\Exception $e) {
                $this->warn("Error importando actor ID {$castMember['id']}: " . $e->getMessage());
            }
        }
        
        $series->people()->sync($castIds);
    }

    private function importActors()
    {
        $this->info('🎭 Actualizando información de actores existentes...');
        
        $actors = Person::whereNotNull('tmdb_id')->get();
        $progressBar = $this->output->createProgressBar($actors->count());
        $progressBar->start();
        
        foreach ($actors as $actor) {
            try {
                $personData = $this->tmdb->getPersonDetails($actor->tmdb_id);
                
                if (!empty($personData)) {
                    $actor->update([
                        'name' => $personData['name'] ?? $actor->name,
                        'display_name' => $personData['name'] ?? $actor->name,
                        'profile_path' => $personData['profile_path'] ?? $actor->profile_path,
                        'birthday' => $personData['birthday'] ? date('Y-m-d', strtotime($personData['birthday'])) : $actor->birthday,
                        'deathday' => $personData['deathday'] ? date('Y-m-d', strtotime($personData['deathday'])) : $actor->deathday,
                        'place_of_birth' => $personData['place_of_birth'] ?? $actor->place_of_birth,
                        'biography' => $personData['biography'] ?? $actor->biography,
                        'popularity' => $personData['popularity'] ?? $actor->popularity,
                        'adult' => $personData['adult'] ?? $actor->adult,
                        'gender' => $personData['gender'] ?? $actor->gender,
                        'known_for_department' => $personData['known_for_department'] ?? $actor->known_for_department,
                        'homepage' => $personData['homepage'] ?? $actor->homepage,
                        'imdb_id' => $personData['imdb_id'] ?? $actor->imdb_id,
                    ]);
                }
                
                usleep(100000); // 100ms delay to avoid rate limiting
                
            } catch (\Exception $e) {
                $this->warn("Error actualizando actor ID {$actor->tmdb_id}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->line('');
        $this->info('🎭 Información de actores actualizada');
    }
}
