<?php

namespace App\Console\Commands;

use App\Models\Person;
use Illuminate\Console\Command;
use OpenAI\Laravel\Facades\OpenAI;
use Exception;

class TranslateActorsSpanish extends Command
{
    protected $signature = 'translate:actors-spanish {--limit=100 : Límite de actores a traducir}';
    protected $description = 'Traducir información de actores coreanos al español';

    public function handle()
    {
        $limit = (int) $this->option('limit');
        
        $this->info("🎭 Traduciendo información de actores coreanos al español");
        $this->info("📊 Límite: {$limit} actores");

        // Obtener actores sin traducir
        $actors = Person::whereNull('name_es')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->orderBy('popularity', 'desc')
            ->limit($limit)
            ->get();

        if ($actors->isEmpty()) {
            $this->info("✅ Todos los actores ya están traducidos");
            return 0;
        }

        $progressBar = $this->output->createProgressBar($actors->count());
        $progressBar->setFormat('%current%/%max% [%bar%] %percent:3s%% - %message%');

        $translated = 0;
        $errors = 0;

        foreach ($actors as $actor) {
            $progressBar->setMessage("Traduciendo: {$actor->name}");

            try {
                $translatedName = $this->translateActorName($actor->name);
                $translatedBio = $this->translateBiography($actor->biography);

                $actor->update([
                    'name_es' => $translatedName,
                    'biography_es' => $translatedBio
                ]);

                $translated++;

            } catch (Exception $e) {
                $errors++;
                $this->warn("Error traduciendo {$actor->name}: " . $e->getMessage());
            }

            $progressBar->advance();
            
            // Pequeña pausa para no saturar la API
            usleep(200000); // 0.2 segundos
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ === RESUMEN DE TRADUCCIÓN ===");
        $this->info("🎭 Actores traducidos: {$translated}");
        $this->info("❌ Errores: {$errors}");

        return 0;
    }

    private function translateActorName($name)
    {
        if (!$name || strlen($name) < 2) {
            return $name;
        }

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un especialista en nombres coreanos. Si el nombre proporcionado es coreano, mantén el nombre original. Si es un nombre occidental o de otro idioma, tradúcelo apropiadamente al español si es necesario. Responde SOLO con el nombre, sin explicaciones.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Nombre del actor: {$name}"
                    ]
                ],
                'max_tokens' => 50,
                'temperature' => 0.1
            ]);

            $translatedName = trim($response['choices'][0]['message']['content']);
            
            // Si la traducción es muy similar al original, mantener el original
            if (levenshtein(strtolower($name), strtolower($translatedName)) <= 2) {
                return $name;
            }

            return $translatedName ?: $name;

        } catch (Exception $e) {
            return $name;
        }
    }

    private function translateBiography($biography)
    {
        if (!$biography || strlen($biography) < 10) {
            return $biography;
        }

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un traductor profesional especializado en biografías de actores coreanos. Traduce la biografía al español de manera fluida y natural, manteniendo todos los detalles importantes. Responde SOLO con la traducción.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Biografía: {$biography}"
                    ]
                ],
                'max_tokens' => 800,
                'temperature' => 0.4
            ]);

            return trim($response['choices'][0]['message']['content']) ?: $biography;

        } catch (Exception $e) {
            return $biography;
        }
    }
}