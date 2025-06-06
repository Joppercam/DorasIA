<?php

namespace App\Console\Commands;

use App\Models\Series;
use App\Services\TmdbService;
use Illuminate\Console\Command;

class ImportTopRatedKoreanDramas extends Command
{
    protected $signature = 'import:top-rated-korean-dramas {--pages=20}';
    protected $description = 'Import top rated Korean dramas from TMDB';

    private TmdbService $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        parent::__construct();
        $this->tmdb = $tmdb;
    }

    public function handle()
    {
        $this->info('🏆 Importing TOP RATED Korean dramas...');
        
        $pages = (int) $this->option('pages');
        $totalImported = 0;
        
        for ($page = 1; $page <= $pages; $page++) {
            $this->info("📄 Processing top rated page {$page} of {$pages}...");
            
            $dramas = $this->tmdb->getTopRatedKoreanDramas($page);
            
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
                    
                    // Get detailed info
                    $details = $this->tmdb->getTvShowDetails($series->tmdb_id);
                    if (!empty($details)) {
                        $series->update([
                            'synopsis' => $details['overview'] ?? $series->overview,
                            'status' => $details['status'] ?? null,
                            'number_of_seasons' => $details['number_of_seasons'] ?? 0,
                            'number_of_episodes' => $details['number_of_episodes'] ?? 0
                        ]);
                    }
                    
                    $totalImported++;
                    $this->line("✅ Imported: {$dramaData['name']} (Rating: {$dramaData['vote_average']})");
                    
                } catch (\Exception $e) {
                    $this->error("❌ Failed: {$dramaData['name']}");
                }
            }
            
            usleep(100000); // 0.1 second delay
        }
        
        $this->info("🎉 Top rated import completed! Total: {$totalImported}");
        return Command::SUCCESS;
    }
}