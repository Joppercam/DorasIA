<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;

class CacheManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:manage {action : The action to perform (clear, warm, stats)} {--type= : The cache type to clear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage application cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $type = $this->option('type');

        switch ($action) {
            case 'clear':
                $this->clearCache($type);
                break;
            
            case 'warm':
                $this->warmCache();
                break;
            
            case 'stats':
                $this->showStats();
                break;
            
            default:
                $this->error("Invalid action. Use 'clear', 'warm', or 'stats'.");
        }
    }

    /**
     * Clear cache
     */
    private function clearCache($type)
    {
        if ($type) {
            CacheService::clearType($type);
            $this->info("Cache cleared for type: {$type}");
        } else {
            CacheService::clearAll();
            $this->info("All cache cleared");
        }
    }

    /**
     * Warm cache
     */
    private function warmCache()
    {
        $this->info("Warming cache...");
        
        $start = microtime(true);
        CacheService::warmCache();
        $duration = round(microtime(true) - $start, 2);
        
        $this->info("Cache warmed successfully in {$duration} seconds");
    }

    /**
     * Show cache statistics
     */
    private function showStats()
    {
        $stats = CacheService::getStats();
        
        $this->info("Cache Statistics:");
        $this->table(
            ['Type', 'Count', 'Memory (MB)'],
            array_map(function($type, $data) {
                return [
                    $type,
                    $data['count'],
                    round($data['memory'] / 1024 / 1024, 2)
                ];
            }, array_keys($stats), $stats)
        );
        
        $totalMemory = array_sum(array_column($stats, 'memory'));
        $this->info("Total cache memory: " . round($totalMemory / 1024 / 1024, 2) . " MB");
    }
}