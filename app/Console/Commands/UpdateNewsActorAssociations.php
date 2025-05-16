<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Models\Person;

class UpdateNewsActorAssociations extends Command
{
    protected $signature = 'news:update-actor-associations';
    protected $description = 'Update existing news with proper actor associations';

    public function handle()
    {
        $this->info('Updating news with actor associations...');
        
        // Obtener todas las noticias
        $news = News::all();
        $updatedCount = 0;
        $totalNewsCount = $news->count();
        $this->info("Procesando {$totalNewsCount} noticias...");
        
        // Lista expandida de actores a buscar
        $actorNames = [
            'Kim Soo-hyun', 'IU', 'Song Hye-kyo', 'Park Seo-joon', 'Ji Chang-wook', 
            'Jun Ji-hyun', 'Lee Min-ho', 'Bae Suzy', 'Hyun Bin', 'Son Ye-jin',
            'Park Bo-gum', 'Kim Ji-won', 'Jung Hae-in', 'Song Joong-ki', 'Gong Yoo',
            'Seo Hyun-jin', 'Park Shin-hye', 'Lee Jong-suk', 'Han Hyo-joo',
            'Lee Jung-jae', 'Park Hae-soo', 'Cha Eun-woo', 'Kim Tae-ri',
            'Nam Joo-hyuk', 'Yoo Jae-suk', 'Jung Ho-yeon', 'Lee Dong-wook',
            'Kim Bum', 'Park Min-young', 'Song Kang', 'Shin Min-ah', 'Kim Woo-bin',
            'Kang Ha-neul', 'Nam Ji-hyun', 'Go Hyun-jung', 'Ahn Hyo-seop',
            'Kim Se-jeong', 'Do Kyung-soo', 'Kim Hae-sook', 'Choi Min-sik',
            'Park Bo-young', 'Lee Jun-ki', 'Yoo Ah-in', 'Lee Byung-hun',
            'Kim Nam-joo', 'Park Hae-jin', 'Lee Joon-gi', 'Jo In-sung',
            'Won Bin', 'Jang Geun-suk', 'Lee Seung-gi', 'Kim Myung-min',
            'Park Yoo-chun', 'Lee Sun-kyun', 'Ryu Jun-yeol', 'Kang Dong-won',
            'Ha Jung-woo', 'So Ji-sub', 'Jang Dong-gun', 'Lee Ji-ah',
            'Kim Ha-neul', 'Han Ji-min', 'Moon Chae-won', 'Park Bo-young',
            'Jeon Do-yeon', 'Kim Hee-sun', 'Go Ara', 'Han Ga-in'
        ];
        
        foreach ($news as $newsItem) {
            $attachedActors = [];
            $textToSearch = $newsItem->title . ' ' . $newsItem->content;
            
            foreach ($actorNames as $actorName) {
                if (stripos($textToSearch, $actorName) !== false) {
                    // Búsqueda case-insensitive
                    $actor = Person::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($actorName) . '%'])->first();
                    
                    if (!$actor) {
                        // Si no existe, intentar crearla
                        $this->info("Actor no encontrado: {$actorName}. Creando...");
                        $actor = Person::create([
                            'name' => $actorName,
                            'slug' => \Illuminate\Support\Str::slug($actorName),
                            'tmdb_id' => null,
                            'gender' => 0
                        ]);
                    }
                    
                    if ($actor && !in_array($actor->id, $attachedActors)) {
                        // Verificar si ya está asociado
                        if (!$newsItem->people()->where('people.id', $actor->id)->exists()) {
                            $newsItem->people()->attach($actor->id, [
                                'primary_subject' => count($attachedActors) == 0
                            ]);
                            $attachedActors[] = $actor->id;
                            $this->info("Asociado {$actorName} a: {$newsItem->title}");
                        }
                    }
                }
            }
            
            if (count($attachedActors) > 0) {
                $this->info("Actualizada noticia: {$newsItem->title} (Asociada con " . count($attachedActors) . " actores)");
                $updatedCount++;
            }
        }
        
        $this->info("Proceso completado. {$updatedCount} noticias actualizadas con asociaciones de actores.");
    }
}