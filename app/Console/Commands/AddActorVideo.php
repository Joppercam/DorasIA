<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Person;
use App\Models\ActorContent;

class AddActorVideo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actors:add-video 
                            {actor_id : ID del actor} 
                            {video_url : URL del video (TikTok, YouTube, Instagram)} 
                            {--title= : Título del contenido} 
                            {--type=video : Tipo de contenido (video, interview, behind_scenes)} 
                            {--description= : Descripción del contenido}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Añade un video externo (TikTok, YouTube, Instagram) a un actor';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $actorId = $this->argument('actor_id');
        $videoUrl = $this->argument('video_url');
        
        // Verificar que el actor existe
        $actor = Person::find($actorId);
        if (!$actor) {
            $this->error("❌ Actor con ID {$actorId} no encontrado.");
            return 1;
        }

        // Detectar el tipo de plataforma
        $videoType = $this->detectVideoType($videoUrl);
        if (!$videoType) {
            $this->error("❌ URL de video no válida. Soportamos TikTok, YouTube e Instagram.");
            return 1;
        }

        $this->info("🎬 Añadiendo video {$videoType} para {$actor->display_name}...");

        // Obtener datos del contenido
        $title = $this->option('title') ?: $this->ask('Título del contenido', "Video exclusivo - {$actor->display_name}");
        $type = $this->option('type') ?: $this->choice('Tipo de contenido', ['video', 'interview', 'behind_scenes'], 'video');
        $description = $this->option('description') ?: $this->ask('Descripción del contenido', "Contenido exclusivo de {$actor->display_name} en {$videoType}.");

        // Estimar duración según la plataforma
        $duration = $this->estimateDuration($videoType);

        try {
            $content = ActorContent::create([
                'person_id' => $actor->id,
                'type' => $type,
                'title' => $title,
                'content' => $description,
                'external_video_url' => $videoUrl,
                'external_video_type' => $videoType,
                'duration' => $duration,
                'is_exclusive' => true,
                'is_featured' => $this->confirm('¿Marcar como contenido destacado?', true),
                'published_at' => now(),
                'source' => "Dorasia {$videoType}",
                'tags' => [ucfirst($type), ucfirst($videoType), 'Exclusivo', $actor->display_name],
                'view_count' => rand(100, 1000),
                'like_count' => rand(10, 100),
                'metadata' => [
                    'platform' => $videoType,
                    'original_url' => $videoUrl,
                    'added_via' => 'command'
                ]
            ]);

            $this->info("✅ Video añadido exitosamente!");
            $this->table(['Campo', 'Valor'], [
                ['ID', $content->id],
                ['Actor', $actor->display_name],
                ['Título', $content->title],
                ['Tipo', $content->type_name],
                ['Plataforma', strtoupper($videoType)],
                ['URL', $videoUrl],
                ['Duración', $content->formatted_duration],
                ['Destacado', $content->is_featured ? 'Sí' : 'No']
            ]);

            $contentUrl = route('actors.content.show', [$actor->id, $content->id]);
            $this->info("🔗 Ver contenido en: {$contentUrl}");

        } catch (\Exception $e) {
            $this->error("❌ Error al crear el contenido: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Detectar el tipo de plataforma de video
     */
    private function detectVideoType($url)
    {
        if (str_contains($url, 'tiktok.com')) {
            return 'tiktok';
        }
        
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            return 'youtube';
        }
        
        if (str_contains($url, 'instagram.com')) {
            return 'instagram';
        }
        
        return null;
    }

    /**
     * Estimar duración según la plataforma
     */
    private function estimateDuration($type)
    {
        switch ($type) {
            case 'tiktok':
                return rand(15, 180); // 15 segundos a 3 minutos
            case 'youtube':
                return rand(300, 1800); // 5 a 30 minutos
            case 'instagram':
                return rand(15, 60); // 15 segundos a 1 minuto
            default:
                return 60;
        }
    }
}