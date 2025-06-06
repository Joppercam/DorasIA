<?php

namespace App\Console\Commands;

use App\Models\Series;
use App\Models\Genre;
use App\Services\TmdbService;
use Illuminate\Console\Command;

class ImportKoreanDramasByGenre extends Command
{
    protected $signature = 'import:korean-dramas-by-genre {--genre=* : Genre IDs to import} {--pages=10}';
    protected $description = 'Import Korean dramas by specific genres';

    private TmdbService $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        parent::__construct();
        $this->tmdb = $tmdb;
    }

    public function handle()
    {
        $this->info('🎭 Importing Korean dramas by GENRES...');
        
        $genreIds = $this->option('genre');
        $pages = (int) $this->option('pages');
        
        // Popular K-drama genres
        $popularGenres = [
            18, // Drama
            35, // Comedy
            10759, // Action & Adventure
            9648, // Mystery
            10766, // Soap
            10765, // Sci-Fi & Fantasy
            10762, // Kids
            10751 // Family
        ];
        
        if (empty($genreIds)) {
            $genreIds = $popularGenres;
        }
        
        $totalImported = 0;
        
        foreach ($genreIds as $genreId) {
            $genre = Genre::where('tmdb_id', $genreId)->first();
            $genreName = $genre ? $genre->name : "Genre {$genreId}";
            
            $this->info("🎬 Processing genre: {$genreName}");
            
            for ($page = 1; $page <= $pages; $page++) {
                $dramas = $this->tmdb->discoverKoreanDramas([
                    'with_genres' => $genreId,
                    'page' => $page,
                    'sort_by' => 'popularity.desc'
                ]);
                
                if (empty($dramas['results'])) {
                    continue;
                }
                
                foreach ($dramas['results'] as $dramaData) {
                    try {
                        $existingSeries = Series::where('tmdb_id', $dramaData['id'])->first();
                        if ($existingSeries) {
                            continue;
                        }
                        
                        $series = Series::create([
                            'title' => $dramaData['name'],
                            'original_title' => $dramaData['original_name'] ?? $dramaData['name'],
                            'overview' => $dramaData['overview'] ?? null,
                            'first_air_date' => $dramaData['first_air_date'] ?? null,
                            'vote_average' => $dramaData['vote_average'] ?? 0,
                            'vote_count' => $dramaData['vote_count'] ?? 0,
                            'popularity' => $dramaData['popularity'] ?? 0,
                            'poster_path' => $dramaData['poster_path'] ?? null,
                            'backdrop_path' => $dramaData['backdrop_path'] ?? null,
                            'original_language' => $dramaData['original_language'] ?? 'ko',
                            'origin_country' => is_array($dramaData['origin_country'] ?? []) ? 
                                implode(',', $dramaData['origin_country']) : 'KR',
                            'tmdb_id' => $dramaData['id'],
                            'is_korean_drama' => true
                        ]);
                        
                        $totalImported++;
                        $this->line("✅ [{$genreName}] {$dramaData['name']}");
                        
                    } catch (\Exception $e) {
                        $this->error("❌ Failed: {$dramaData['name']}");
                    }
                }
                
                usleep(100000);
            }
        }
        
        $this->info("🎉 Genre import completed! Total: {$totalImported}");
        return Command::SUCCESS;
    }
}
