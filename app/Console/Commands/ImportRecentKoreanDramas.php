<?php

namespace App\Console\Commands;

use App\Models\Series;
use App\Services\TmdbService;
use Illuminate\Console\Command;

class ImportRecentKoreanDramas extends Command
{
    protected $signature = 'import:recent-korean-dramas {--pages=30}';
    protected $description = 'Import recent Korean dramas from TMDB';

    private TmdbService $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        parent::__construct();
        $this->tmdb = $tmdb;
    }

    public function handle()
    {
        $this->info('🆕 Importing RECENT Korean dramas...');
        
        $pages = (int) $this->option('pages');
        $totalImported = 0;
        
        for ($page = 1; $page <= $pages; $page++) {
            $this->info("📄 Processing recent page {$page} of {$pages}...");
            
            $dramas = $this->tmdb->getRecentKoreanDramas($page);
            
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
                    $this->line("✅ Imported: {$dramaData['name']} ({$dramaData['first_air_date']})");
                    
                } catch (\Exception $e) {
                    $this->error("❌ Failed: {$dramaData['name']}");
                }
            }
            
            usleep(100000);
        }
        
        $this->info("🎉 Recent import completed! Total: {$totalImported}");
        return Command::SUCCESS;
    }
}