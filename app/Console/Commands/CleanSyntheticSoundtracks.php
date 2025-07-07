<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Soundtrack;

class CleanSyntheticSoundtracks extends Command
{
    protected $signature = 'soundtracks:clean-synthetic 
                            {--dry-run : Preview cleanup without deleting}
                            {--force : Force deletion without confirmation}';

    protected $description = 'Clean synthetic/fake soundtracks, keeping only authentic verified ones';

    // Patrones que identifican soundtracks sintéticos
    private $syntheticPatterns = [
        'titles' => [
            'Opening Theme',
            'Love Theme', 
            'Dramatic Moment',
            'Ending Credits',
            'Main Theme',
            'Background Music',
            'Instrumental'
        ],
        'artists' => [
            'Various Artists',
            'Unknown Artist',
            'Soundtrack'
        ]
    ];

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $force = $this->option('force');
        
        $this->showHeader();
        
        if ($isDryRun) {
            $this->warn('⚠️  MODO PREVIEW - No se eliminarán soundtracks');
        }
        
        // Encontrar soundtracks sintéticos
        $syntheticSoundtracks = $this->findSyntheticSoundtracks();
        
        $this->info("🔍 ANÁLISIS DE SOUNDTRACKS:");
        $this->showAnalysis($syntheticSoundtracks);
        
        if ($syntheticSoundtracks->isEmpty()) {
            $this->info("✅ No se encontraron soundtracks sintéticos para limpiar");
            return;
        }
        
        // Mostrar preview de lo que se eliminará
        $this->showCleanupPreview($syntheticSoundtracks);
        
        if (!$isDryRun) {
            if (!$force && !$this->confirm('¿Confirmas eliminar estos soundtracks sintéticos?')) {
                $this->info('❌ Operación cancelada');
                return;
            }
            
            $deleted = $this->performCleanup($syntheticSoundtracks);
            $this->showCleanupResults($deleted);
        }
    }
    
    private function showHeader()
    {
        $this->info('🧹 ===== LIMPIEZA DE SOUNDTRACKS SINTÉTICOS =====');
        $this->info('🎯 Removiendo soundtracks no auténticos');
        $this->info('✅ Manteniendo solo contenido verificado');
        $this->line('');
    }
    
    private function findSyntheticSoundtracks()
    {
        $synthetic = collect();
        
        // 1. Soundtracks sin YouTube ID (no reproducibles)
        $withoutYoutube = Soundtrack::whereNull('youtube_id')
                                  ->orWhere('youtube_id', '')
                                  ->get();
        
        // 2. Soundtracks con títulos genéricos
        $genericTitles = Soundtrack::where(function($query) {
            foreach ($this->syntheticPatterns['titles'] as $pattern) {
                $query->orWhere('title', 'LIKE', "%{$pattern}%");
            }
        })->get();
        
        // 3. Soundtracks con artistas genéricos
        $genericArtists = Soundtrack::where(function($query) {
            foreach ($this->syntheticPatterns['artists'] as $pattern) {
                $query->orWhere('artist', 'LIKE', "%{$pattern}%");
            }
        })->get();
        
        // 4. Soundtracks con popularidad baja (< 9.0 indica sintético)
        $lowPopularity = Soundtrack::where('popularity', '<', 9.0)->get();
        
        // Combinar todos los resultados sin duplicados
        $synthetic = $withoutYoutube
            ->merge($genericTitles)
            ->merge($genericArtists)
            ->merge($lowPopularity)
            ->unique('id');
        
        return $synthetic;
    }
    
    private function showAnalysis($syntheticSoundtracks)
    {
        $totalSoundtracks = Soundtrack::count();
        $authenticSoundtracks = Soundtrack::where('popularity', '>=', 9.0)
                                        ->whereNotNull('youtube_id')
                                        ->where('youtube_id', '!=', '')
                                        ->count();
        
        $this->table(
            ['Métrica', 'Cantidad', 'Porcentaje'],
            [
                ['Total soundtracks', $totalSoundtracks, '100%'],
                ['Auténticos (≥9.0 pop + YouTube)', $authenticSoundtracks, round(($authenticSoundtracks/$totalSoundtracks)*100, 1).'%'],
                ['Sintéticos detectados', $syntheticSoundtracks->count(), round(($syntheticSoundtracks->count()/$totalSoundtracks)*100, 1).'%'],
                ['Permanecerán', $totalSoundtracks - $syntheticSoundtracks->count(), round((($totalSoundtracks - $syntheticSoundtracks->count())/$totalSoundtracks)*100, 1).'%']
            ]
        );
    }
    
    private function showCleanupPreview($syntheticSoundtracks)
    {
        $this->warn("\n🗑️  SOUNDTRACKS SINTÉTICOS A ELIMINAR:");
        
        // Agrupar por razón de eliminación
        $reasons = [
            'Sin YouTube ID' => $syntheticSoundtracks->filter(function($s) {
                return empty($s->youtube_id);
            }),
            'Títulos genéricos' => $syntheticSoundtracks->filter(function($s) {
                foreach ($this->syntheticPatterns['titles'] as $pattern) {
                    if (str_contains($s->title, $pattern)) return true;
                }
                return false;
            }),
            'Artistas genéricos' => $syntheticSoundtracks->filter(function($s) {
                foreach ($this->syntheticPatterns['artists'] as $pattern) {
                    if (str_contains($s->artist, $pattern)) return true;
                }
                return false;
            }),
            'Popularidad baja' => $syntheticSoundtracks->filter(function($s) {
                return $s->popularity < 9.0;
            })
        ];
        
        foreach ($reasons as $reason => $tracks) {
            if ($tracks->count() > 0) {
                $this->line("\n📋 {$reason} ({$tracks->count()} tracks):");
                foreach ($tracks->take(5) as $track) {
                    $contentType = $track->soundtrackable_type === 'App\Models\Series' ? '📺' : '🎬';
                    $contentTitle = $track->soundtrackable ? $track->soundtrackable->display_title : 'N/A';
                    $this->line("   ❌ {$track->title} - {$track->artist} ({$contentType} {$contentTitle})");
                }
                if ($tracks->count() > 5) {
                    $this->line("   ... y " . ($tracks->count() - 5) . " más");
                }
            }
        }
        
        $this->line("\n✅ SOUNDTRACKS AUTÉNTICOS QUE SE MANTENDRÁN:");
        $authentic = Soundtrack::where('popularity', '>=', 9.0)
                              ->whereNotNull('youtube_id')
                              ->where('youtube_id', '!=', '')
                              ->whereNotIn('id', $syntheticSoundtracks->pluck('id'))
                              ->take(10)
                              ->get();
        
        foreach ($authentic as $track) {
            $contentType = $track->soundtrackable_type === 'App\Models\Series' ? '📺' : '🎬';
            $contentTitle = $track->soundtrackable ? $track->soundtrackable->display_title : 'N/A';
            $this->line("   ✅ {$track->title} - {$track->artist} ({$contentType} {$contentTitle}) [Pop: {$track->popularity}]");
        }
    }
    
    private function performCleanup($syntheticSoundtracks)
    {
        $this->info("\n🧹 EJECUTANDO LIMPIEZA...");
        
        $deleted = 0;
        $errors = 0;
        
        foreach ($syntheticSoundtracks as $soundtrack) {
            try {
                $soundtrack->delete();
                $deleted++;
                
                if ($deleted % 10 == 0) {
                    $this->line("   🗑️  Eliminados: {$deleted}");
                }
            } catch (\Exception $e) {
                $errors++;
                $this->warn("   ❌ Error eliminando: {$soundtrack->title} - {$e->getMessage()}");
            }
        }
        
        return ['deleted' => $deleted, 'errors' => $errors];
    }
    
    private function showCleanupResults($results)
    {
        $this->info("\n📊 RESULTADOS DE LIMPIEZA:");
        
        $totalAfter = Soundtrack::count();
        $authenticAfter = Soundtrack::where('popularity', '>=', 9.0)
                                  ->whereNotNull('youtube_id')
                                  ->where('youtube_id', '!=', '')
                                  ->count();
        
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Soundtracks eliminados', $results['deleted']],
                ['Errores durante eliminación', $results['errors']],
                ['Total soundtracks restantes', $totalAfter],
                ['Soundtracks auténticos', $authenticAfter],
                ['Porcentaje auténtico', round(($authenticAfter/$totalAfter)*100, 1).'%']
            ]
        );
        
        $this->info("\n✅ LIMPIEZA COMPLETADA!");
        $this->info("🎵 La base de datos ahora contiene solo soundtracks auténticos y verificados");
        $this->info("📱 Prueba en: http://127.0.0.1:8000/movies/7 (Tu Nombre)");
        
        if ($results['errors'] > 0) {
            $this->warn("⚠️  Se encontraron {$results['errors']} errores durante la eliminación");
        }
    }
}