<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Series;
use App\Services\TranslationService;

class TranslateExistingContent extends Command
{
    protected $signature = 'translate:content 
                            {--type=all : Tipo de contenido (titles, synopsis, all)}
                            {--limit=50 : Número de series a procesar por lote}
                            {--force : Forzar traducción aunque ya exista}'
    ;
    
    protected $description = 'Traduce contenido existente de series al español chileno usando OpenAI';

    public function handle()
    {
        $translationService = new TranslationService();
        
        if (!$translationService->isAvailable()) {
            $this->error('❌ Servicio de traducción no disponible. Verifica OPENAI_API_KEY en .env');
            return Command::FAILURE;
        }

        $type = $this->option('type');
        $limit = (int) $this->option('limit');
        $force = $this->option('force');

        $this->info("🚀 Iniciando traducción de contenido...");
        $this->info("📊 Tipo: {$type}, Límite: {$limit}, Forzar: " . ($force ? "Sí" : "No"));

        $query = Series::query();
        
        // Filtrar series que necesitan traducción
        if (!$force) {
            if ($type === 'titles' || $type === 'all') {
                $query->where(function($q) {
                    $q->whereNull('title_es')->orWhere('title_es', '');
                });
            }
            if ($type === 'synopsis' || $type === 'all') {
                $query->where(function($q) {
                    $q->whereNull('overview_es')->orWhere('overview_es', '');
                });
            }
        }

        $totalSeries = $query->count();
        $this->info("📚 Series a procesar: {$totalSeries}");

        if ($totalSeries === 0) {
            $this->info("✅ No hay contenido para traducir");
            return Command::SUCCESS;
        }

        $progressBar = $this->output->createProgressBar($totalSeries);
        $progressBar->start();

        $processed = 0;
        $successful = 0;
        $errors = 0;

        $query->chunk($limit, function ($series) use (
            $translationService, 
            $type, 
            $force, 
            &$progressBar, 
            &$processed, 
            &$successful, 
            &$errors
        ) {
            foreach ($series as $serie) {
                try {
                    $updated = false;

                    // Traducir título
                    if (($type === 'titles' || $type === 'all') && 
                        ($force || empty($serie->title_es))) {
                        
                        if ($serie->title) {
                            $translatedTitle = $translationService->translateToChileanSpanish(
                                $serie->title, 
                                'title'
                            );
                            
                            if ($translatedTitle) {
                                $serie->title_es = $translatedTitle;
                                $updated = true;
                            }
                        }
                    }

                    // Traducir sinopsis
                    if (($type === 'synopsis' || $type === 'all') && 
                        ($force || empty($serie->overview_es))) {
                        
                        if ($serie->overview) {
                            $translatedOverview = $translationService->translateToChileanSpanish(
                                $serie->overview, 
                                'synopsis'
                            );
                            
                            if ($translatedOverview) {
                                $serie->overview_es = $translatedOverview;
                                $updated = true;
                            }
                        }
                    }

                    // Traducir tagline
                    if (($type === 'all') && 
                        ($force || empty($serie->tagline_es))) {
                        
                        if ($serie->tagline) {
                            $translatedTagline = $translationService->translateToChileanSpanish(
                                $serie->tagline, 
                                'tagline'
                            );
                            
                            if ($translatedTagline) {
                                $serie->tagline_es = $translatedTagline;
                                $updated = true;
                            }
                        }
                    }

                    if ($updated) {
                        $serie->save();
                        $successful++;
                    }

                    // Pequeña pausa para no sobrecargar la API
                    usleep(200000); // 0.2 segundos

                } catch (\Exception $e) {
                    $errors++;
                    $this->newLine();
                    $this->error("❌ Error traduciendo serie {$serie->id}: " . $e->getMessage());
                }

                $processed++;
                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ Traducción completada:");
        $this->info("📊 Series procesadas: {$processed}");
        $this->info("✅ Traducciones exitosas: {$successful}");
        $this->info("❌ Errores: {$errors}");

        return Command::SUCCESS;
    }
}