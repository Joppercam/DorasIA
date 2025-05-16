<?php

namespace App\Console\Commands;

use App\Models\Title;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateStreamingPlatforms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dorasia:update-platforms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza las plataformas de streaming para los títulos';

    /**
     * Mapeo de plataformas por categoría
     */
    protected $platformsByCategoryId = [
        1 => ['netflix', 'viki'], // K-Drama
        2 => ['netflix', 'crunchyroll'], // J-Drama
        3 => ['viki', 'netflix'] // C-Drama
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Actualizando plataformas de streaming para los títulos...');
        
        // Obtener todos los títulos
        $titles = Title::all();
        $updated = 0;
        
        $this->output->progressStart($titles->count());
        
        foreach ($titles as $title) {
            // Si el título ya tiene plataformas, dejarlo como está
            if (!empty($title->streaming_platforms)) {
                $this->output->progressAdvance();
                continue;
            }
            
            // Asignar plataformas basadas en la categoría
            $platformsToAssign = [];
            $categoryId = $title->category_id;
            
            if (isset($this->platformsByCategoryId[$categoryId])) {
                $platformsToAssign = $this->platformsByCategoryId[$categoryId];
            } else {
                // Asignar plataformas aleatorias si no hay mapeo para esta categoría
                $allPlatforms = ['netflix', 'viki', 'disney', 'crunchyroll', 'apple'];
                shuffle($allPlatforms);
                $platformsToAssign = array_slice($allPlatforms, 0, 2);
            }
            
            // Popular plataformas adicionales aleatoriamente
            $randomChance = mt_rand(1, 100);
            if ($randomChance > 50) {
                $extraPlatforms = ['disney', 'apple', 'hbo', 'prime'];
                shuffle($extraPlatforms);
                $extraPlatform = array_slice($extraPlatforms, 0, 1);
                $platformsToAssign = array_merge($platformsToAssign, $extraPlatform);
            }
            
            // Guardar plataformas en el título
            $title->streaming_platforms = $platformsToAssign; // No necesitamos json_encode porque el modelo ya lo hace
            $title->save();
            
            $this->line("Plataformas asignadas para: {$title->title} -> " . implode(', ', $platformsToAssign));
            $updated++;
            
            $this->output->progressAdvance();
        }
        
        $this->output->progressFinish();
        $this->info("Se han actualizado las plataformas de streaming para {$updated} títulos.");
        
        return Command::SUCCESS;
    }
}