<?php

namespace App\Console\Commands;

use App\Models\Series;
use App\Services\TmdbService;
use Illuminate\Console\Command;

class ImportKoreanRomanceDramas extends Command
{
    protected $signature = 'import:korean-romance-dramas {--pages=15}';
    protected $description = 'Import Korean romance dramas with high ratings';

    private TmdbService $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        parent::__construct();
        $this->tmdb = $tmdb;
    }

    public function handle()
    {
        $this->info('💕 Importing KOREAN ROMANCE dramas...');
        
        $pages = (int) $this->option('pages');
        $totalImported = 0;
        
        // Romance filters
        $filters = [
            ['with_genres' => '18,10759', 'sort_by' => 'vote_average.desc', 'vote_count.gte' => 20], // Drama + Action
            ['with_genres' => '18', 'sort_by' => 'popularity.desc', 'with_keywords' => 'romance,love'],
            ['with_genres' => '35,18', 'sort_by' => 'vote_average.desc'], // Comedy + Drama
            ['sort_by' => 'vote_average.desc', 'with_keywords' => 'korean,romance,love,wedding,marriage'],
        ];
        
        foreach ($filters as $filterIndex => $filter) {
            $this->info("💖 Filter set " . ($filterIndex + 1) . " of " . count($filters));
            
            for ($page = 1; $page <= $pages; $page++) {
                $filter['page'] = $page;
                $filter['with_origin_country'] = 'KR';
                
                $dramas = $this->tmdb->discoverKoreanDramas($filter);
                
                if (empty($dramas['results'])) {
                    continue;
                }
                
                foreach ($dramas['results'] as $dramaData) {
                    try {
                        $existingSeries = Series::where('tmdb_id', $dramaData['id'])->first();
                        if ($existingSeries) {
                            continue;
                        }
                        
                        // Check if it seems like a romance
                        $isRomance = $this->isRomanceDrama($dramaData);
                        
                        if (!$isRomance) {
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
                        $this->line("✅ 💕 {$dramaData['name']} (⭐{$dramaData['vote_average']})");
                        
                    } catch (\Exception $e) {
                        $this->error("❌ Failed: {$dramaData['name']}");
                    }
                }
                
                usleep(150000); // 0.15 seconds
            }
        }
        
        $this->info("🎉 Romance import completed! Total: {$totalImported}");
        return Command::SUCCESS;
    }
    
    private function isRomanceDrama(array $dramaData): bool
    {
        $title = strtolower($dramaData['name'] ?? '');
        $overview = strtolower($dramaData['overview'] ?? '');
        
        $romanceKeywords = [
            'love', 'romance', 'marriage', 'wedding', 'dating', 'couple',
            'heart', 'relationship', 'boyfriend', 'girlfriend', 'husband', 'wife',
            'crush', 'fall in love', 'romantic', 'passion', 'affair'
        ];
        
        foreach ($romanceKeywords as $keyword) {
            if (strpos($title, $keyword) !== false || strpos($overview, $keyword) !== false) {
                return true;
            }
        }
        
        // Check if drama genre is included
        $genres = $dramaData['genre_ids'] ?? [];
        return in_array(18, $genres); // Drama genre ID
    }
}