<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Person;
use App\Services\TmdbService;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class UpdateActorImages extends Command
{
    protected $signature = 'actors:update-images 
                            {--update-all : Update all actors with generic images}
                            {--limit=10 : Number of actors to process}';
    
    protected $description = 'Update actor images from TMDB, replacing generic or placeholder images';
    
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
        $this->info('Buscando actores para actualizar imágenes...');
        
        $limit = $this->option('limit');
        
        // Buscar actores con imágenes genéricas o placeholders
        $query = Person::query();
        
        if ($this->option('update-all')) {
            $query->whereNotNull('profile_path');
        } else {
            // Buscar actores con imágenes que parecen placeholder
            $query->where(function($q) {
                $q->whereNull('profile_path')
                  ->orWhere('profile_path', 'LIKE', '%placeholder%')
                  ->orWhere('profile_path', 'LIKE', '%/posters/%')
                  ->orWhere('profile_path', 'LIKE', '%unknown%')
                  ->orWhere('profile_path', 'LIKE', '%default%');
            });
        }
        
        $actors = $query->whereHas('news')
                       ->limit($limit)
                       ->get();
        
        $this->info("Encontrados " . $actors->count() . " actores para actualizar");
        
        $updated = 0;
        $failed = 0;
        
        foreach ($actors as $actor) {
            $this->info("\nProcesando: {$actor->name}...");
            
            // Intentar actualizar la imagen desde TMDB
            if ($this->updateActorImage($actor)) {
                $updated++;
                $this->info("✓ Imagen actualizada para {$actor->name}");
            } else {
                $failed++;
                $this->warn("✗ No se pudo actualizar imagen para {$actor->name}");
            }
        }
        
        $this->info("\n\nResumen:");
        $this->info("- Actores actualizados: {$updated}");
        $this->info("- Fallos: {$failed}");
        
        return 0;
    }
    
    protected function updateActorImage($actor)
    {
        try {
            // Si tiene tmdb_id, buscar directamente
            if ($actor->tmdb_id) {
                $personData = $this->tmdbService->getPersonDetails($actor->tmdb_id);
                
                if (isset($personData['profile_path'])) {
                    return $this->downloadAndSaveImage($actor, $personData['profile_path']);
                }
            }
            
            // Si no tiene tmdb_id o no se encontró imagen, buscar por nombre
            // Como no existe searchPerson, lo implementaremos directamente
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
                    $this->info("TMDB ID actualizado: {$firstResult['id']}");
                }
                
                if (isset($firstResult['profile_path'])) {
                    return $this->downloadAndSaveImage($actor, $firstResult['profile_path']);
                }
            }
            
            return false;
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return false;
        }
    }
    
    protected function downloadAndSaveImage($actor, $tmdbProfilePath)
    {
        try {
            // Usar el método downloadImage del TmdbService
            $localPath = $this->tmdbService->downloadImage($tmdbProfilePath, 'poster');
            
            if ($localPath) {
                // Eliminar imagen anterior si existe y es diferente
                if ($actor->profile_path && $actor->profile_path !== $localPath && file_exists(public_path($actor->profile_path))) {
                    unlink(public_path($actor->profile_path));
                }
                
                // Actualizar actor con la nueva ruta
                $actor->profile_path = $localPath;
                $actor->save();
                
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            $this->error("Error descargando imagen: " . $e->getMessage());
            return false;
        }
    }
}