<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;
use App\Models\Series;
use App\Services\TranslationService;

class TranslateAllContent extends Command
{
    protected $signature = 'translate:all-content';
    protected $description = 'Translate all movies and series content to Spanish';

    public function handle()
    {
        $this->info('🌍 Iniciando traducción masiva de contenido...');

        $translationService = new TranslationService();
        
        if (!$translationService->isAvailable()) {
            $this->error('❌ Servicio de traducción no disponible');
            return 1;
        }

        // Translate Series
        $this->translateSeries($translationService);
        
        // Translate Movies  
        $this->translateMovies($translationService);

        $this->info('✅ ¡Traducción masiva completada!');
        return 0;
    }

    private function translateSeries($translationService)
    {
        $this->info('📺 Traduciendo series...');

        $seriesToTranslate = Series::where(function($query) {
            $query->whereNull('title_es')
                  ->orWhere('title_es', '')
                  ->orWhereNull('overview_es')
                  ->orWhere('overview_es', '');
        })->get();

        $this->info("Series a traducir: {$seriesToTranslate->count()}");

        $progressBar = $this->output->createProgressBar($seriesToTranslate->count());
        $progressBar->start();

        foreach ($seriesToTranslate as $series) {
            $updated = false;

            // Translate title
            if (empty($series->title_es)) {
                $spanishTitle = $translationService->translateTitle($series->original_title ?: $series->title);
                if ($spanishTitle) {
                    $series->title_es = $spanishTitle;
                    $updated = true;
                }
            }

            // Translate overview
            if (empty($series->overview_es) && !empty($series->overview)) {
                $spanishOverview = $translationService->translateSynopsis($series->overview);
                if ($spanishOverview) {
                    $series->overview_es = $spanishOverview;
                    $updated = true;
                }
            }

            if ($updated) {
                $series->save();
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("✅ Series traducidas exitosamente");
    }

    private function translateMovies($translationService)
    {
        $this->info('🎬 Traduciendo películas...');

        $moviesToTranslate = Movie::where(function($query) {
            $query->whereNull('spanish_title')
                  ->orWhere('spanish_title', '')
                  ->orWhereNull('spanish_overview')
                  ->orWhere('spanish_overview', '');
        })->get();

        $this->info("Películas a traducir: {$moviesToTranslate->count()}");

        $progressBar = $this->output->createProgressBar($moviesToTranslate->count());
        $progressBar->start();

        foreach ($moviesToTranslate as $movie) {
            $updated = false;

            // Translate title
            if (empty($movie->spanish_title)) {
                $spanishTitle = $translationService->translateTitle($movie->original_title ?: $movie->title);
                if ($spanishTitle) {
                    $movie->spanish_title = $spanishTitle;
                    $updated = true;
                }
            }

            // Translate overview
            if (empty($movie->spanish_overview) && !empty($movie->overview)) {
                $spanishOverview = $translationService->translateSynopsis($movie->overview);
                if ($spanishOverview) {
                    $movie->spanish_overview = $spanishOverview;
                    $updated = true;
                }
            }

            if ($updated) {
                $movie->save();
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("✅ Películas traducidas exitosamente");
    }
}