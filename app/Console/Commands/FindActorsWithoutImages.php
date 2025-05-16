<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Models\Person;
use App\Services\TmdbService;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class FindActorsWithoutImages extends Command
{
    protected $signature = 'actors:find-missing-images {--fix : Download and assign missing images}';
    protected $description = 'Find actors in news without profile images and optionally download them';
    
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
        $this->info('Buscando actores sin imágenes en noticias...');
        
        // Obtener todos los actores que están relacionados con noticias
        $actorsInNews = Person::whereHas('news')->get();
        
        $actorsWithoutImages = [];
        
        foreach ($actorsInNews as $actor) {
            // Verificar si el actor no tiene imagen
            if (empty($actor->profile_path) && empty($actor->photo)) {
                $newsCount = $actor->news()->count();
                $actorsWithoutImages[] = [
                    'actor' => $actor,
                    'news_count' => $newsCount
                ];
                
                $this->warn("Actor sin imagen: {$actor->name} (ID: {$actor->id}) - Aparece en {$newsCount} noticias");
            }
        }
        
        $this->info(PHP_EOL . "Total de actores sin imagen: " . count($actorsWithoutImages));
        
        if ($this->option('fix') && count($actorsWithoutImages) > 0) {
            $this->info(PHP_EOL . 'Iniciando descarga de imágenes...');
            $this->fixMissingImages($actorsWithoutImages);
        }
        
        return 0;
    }
    
    protected function fixMissingImages($actorsWithoutImages)
    {
        foreach ($actorsWithoutImages as $data) {
            $actor = $data['actor'];
            $this->info(PHP_EOL . "Procesando: {$actor->name}...");
            
            try {
                // Primero intentar buscar en TMDB si tiene tmdb_id
                if ($actor->tmdb_id) {
                    $this->info("Buscando en TMDB con ID: {$actor->tmdb_id}");
                    $imageUrl = $this->getImageFromTmdb($actor->tmdb_id);
                    
                    if ($imageUrl) {
                        $this->downloadAndSaveImage($actor, $imageUrl);
                        continue;
                    }
                }
                
                // Si no tiene tmdb_id o no se encontró imagen, buscar por nombre
                $this->info("Buscando por nombre en TMDB: {$actor->name}");
                $searchResults = $this->searchActorInTmdb($actor->name);
                
                if ($searchResults && count($searchResults) > 0) {
                    // Usar el primer resultado
                    $firstResult = $searchResults[0];
                    
                    if (isset($firstResult['profile_path'])) {
                        $imageUrl = 'https://image.tmdb.org/t/p/w500' . $firstResult['profile_path'];
                        $this->downloadAndSaveImage($actor, $imageUrl);
                        
                        // Actualizar tmdb_id si no lo tenía
                        if (!$actor->tmdb_id) {
                            $actor->tmdb_id = $firstResult['id'];
                            $actor->save();
                            $this->info("TMDB ID actualizado: {$firstResult['id']}");
                        }
                    }
                }
                
                // Si aún no se encontró imagen, intentar búsqueda en Google Images (básica)
                if (empty($actor->profile_path)) {
                    $this->warn("No se encontró imagen en TMDB para {$actor->name}");
                    // Aquí podrías implementar búsqueda en otras fuentes
                }
                
            } catch (\Exception $e) {
                $this->error("Error procesando {$actor->name}: " . $e->getMessage());
            }
        }
    }
    
    protected function getImageFromTmdb($tmdbId)
    {
        try {
            $response = $this->httpClient->get("https://api.themoviedb.org/3/person/{$tmdbId}", [
                'query' => [
                    'api_key' => env('TMDB_API_KEY'),
                    'language' => 'es-ES'
                ]
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            if (isset($data['profile_path'])) {
                return 'https://image.tmdb.org/t/p/w500' . $data['profile_path'];
            }
        } catch (GuzzleException $e) {
            $this->error("Error en TMDB API: " . $e->getMessage());
        }
        
        return null;
    }
    
    protected function searchActorInTmdb($name)
    {
        try {
            $response = $this->httpClient->get("https://api.themoviedb.org/3/search/person", [
                'query' => [
                    'api_key' => env('TMDB_API_KEY'),
                    'query' => $name,
                    'language' => 'es-ES'
                ]
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            if (isset($data['results'])) {
                return $data['results'];
            }
        } catch (GuzzleException $e) {
            $this->error("Error buscando en TMDB: " . $e->getMessage());
        }
        
        return [];
    }
    
    protected function downloadAndSaveImage($actor, $imageUrl)
    {
        try {
            // Crear directorio si no existe
            $directory = storage_path('app/public/actors');
            if (!file_exists($directory)) {
                mkdir($directory, 0775, true);
            }
            
            // Generar nombre de archivo
            $filename = Str::slug($actor->name) . '-' . time() . '.jpg';
            $filepath = $directory . '/' . $filename;
            
            // Descargar imagen
            $response = $this->httpClient->get($imageUrl);
            file_put_contents($filepath, $response->getBody());
            
            // Actualizar actor con la ruta de la imagen
            $publicPath = 'storage/actors/' . $filename;
            $actor->profile_path = $publicPath;
            $actor->save();
            
            $this->info("✓ Imagen descargada y guardada: {$publicPath}");
            
        } catch (\Exception $e) {
            $this->error("Error descargando imagen: " . $e->getMessage());
        }
    }
}