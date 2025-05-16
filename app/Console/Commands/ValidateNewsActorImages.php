<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Models\Person;
use App\Services\TmdbService;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class ValidateNewsActorImages extends Command
{
    protected $signature = 'news:validate-actor-images 
                            {--fix : Download missing actor images}
                            {--update-generic : Update generic/placeholder images}';
    
    protected $description = 'Validate that all news have proper actor images and fix missing ones';
    
    protected $tmdbService;
    protected $httpClient;
    
    public function __construct(TmdbService $tmdbService)
    {
        parent::__construct();
        $this->tmdbService = $tmdbService;
        $this->httpClient = new Client();
    }
    
    public function handle()
    {
        $this->info('Validando imágenes de actores en noticias...');
        
        // Obtener todas las noticias con sus actores
        $newsWithActors = News::with('people')->get();
        
        $totalNews = $newsWithActors->count();
        $newsWithoutActorImages = 0;
        $actorsWithoutImages = [];
        
        foreach ($newsWithActors as $news) {
            $hasActorWithImage = false;
            
            foreach ($news->people as $person) {
                if ($person->profile_path && file_exists(public_path($person->profile_path))) {
                    // Verificar si es una imagen genérica
                    if ($this->isGenericImage($person->profile_path)) {
                        if ($this->option('update-generic')) {
                            $actorsWithoutImages[$person->id] = $person;
                        }
                    } else {
                        $hasActorWithImage = true;
                    }
                } else {
                    $actorsWithoutImages[$person->id] = $person;
                }
            }
            
            if (!$hasActorWithImage) {
                $newsWithoutActorImages++;
                $this->warn("Noticia sin imagen de actor válida: {$news->title}");
            }
        }
        
        $this->info("\nResumen:");
        $this->info("- Total de noticias: {$totalNews}");
        $this->info("- Noticias sin imagen de actor válida: {$newsWithoutActorImages}");
        $this->info("- Actores únicos sin imagen o con placeholder: " . count($actorsWithoutImages));
        
        if ($this->option('fix') && count($actorsWithoutImages) > 0) {
            $this->info("\nIniciando actualización de imágenes de actores...");
            $this->updateActorImages($actorsWithoutImages);
        }
        
        // Ejecutar comando de actualización de imágenes si es necesario
        if (count($actorsWithoutImages) > 0) {
            $this->call('actors:update-images', [
                '--limit' => count($actorsWithoutImages),
                '--update-all' => $this->option('update-generic')
            ]);
        }
        
        return 0;
    }
    
    protected function isGenericImage($path)
    {
        // Patterns that indicate generic/placeholder images
        if (preg_match('/placeholder|default|unknown/i', $path)) {
            return true;
        }
        
        // Check if it's a generic poster path (simple name in /posters/)
        if (preg_match('#^/?posters/[a-z-]+\.(jpg|png)$#i', $path)) {
            return true;
        }
        
        return false;
    }
    
    protected function updateActorImages($actors)
    {
        $updated = 0;
        $failed = 0;
        
        foreach ($actors as $actor) {
            $this->info("Procesando: {$actor->name}...");
            
            try {
                if ($this->updateActorImage($actor)) {
                    $updated++;
                    $this->info("✓ Imagen actualizada");
                } else {
                    $failed++;
                    $this->warn("✗ No se pudo actualizar");
                }
            } catch (\Exception $e) {
                $failed++;
                $this->error("Error: " . $e->getMessage());
            }
        }
        
        $this->info("\nResultados de actualización:");
        $this->info("- Actualizados: {$updated}");
        $this->info("- Fallos: {$failed}");
    }
    
    protected function updateActorImage($actor)
    {
        try {
            // Si tiene tmdb_id, buscar directamente
            if ($actor->tmdb_id) {
                $personData = $this->tmdbService->getPersonDetails($actor->tmdb_id);
                
                if (isset($personData['profile_path'])) {
                    $localPath = $this->tmdbService->downloadImage($personData['profile_path'], 'poster');
                    
                    if ($localPath) {
                        $actor->profile_path = $localPath;
                        $actor->save();
                        return true;
                    }
                }
            }
            
            // Si no tiene tmdb_id o no se encontró imagen, buscar por nombre
            $searchUrl = "https://api.themoviedb.org/3/search/person";
            $response = $this->httpClient->get($searchUrl, [
                'query' => [
                    'api_key' => config('tmdb.api_key'),
                    'query' => $actor->name,
                    'language' => 'es-ES'
                ]
            ]);
            
            $searchResults = json_decode($response->getBody(), true);
            
            if ($searchResults && count($searchResults['results']) > 0) {
                $firstResult = $searchResults['results'][0];
                
                // Actualizar tmdb_id si no lo tenía
                if (!$actor->tmdb_id) {
                    $actor->tmdb_id = $firstResult['id'];
                    $actor->save();
                }
                
                if (isset($firstResult['profile_path'])) {
                    $localPath = $this->tmdbService->downloadImage($firstResult['profile_path'], 'poster');
                    
                    if ($localPath) {
                        $actor->profile_path = $localPath;
                        $actor->save();
                        return true;
                    }
                }
            }
            
            return false;
            
        } catch (\Exception $e) {
            $this->error("Error actualizando imagen de {$actor->name}: " . $e->getMessage());
            return false;
        }
    }
}