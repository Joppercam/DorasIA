<?php

// Script para crear sistema de traducción automática con OpenAI

require_once 'vendor/autoload.php';

$envContent = file_get_contents('.env');

// Agregar configuración de OpenAI al .env si no existe
if (!str_contains($envContent, 'OPENAI_API_KEY')) {
    $envContent .= "\n# OpenAI Configuration\n";
    $envContent .= "OPENAI_API_KEY=your_openai_api_key_here\n";
    $envContent .= "OPENAI_MODEL=gpt-3.5-turbo\n";
    file_put_contents('.env', $envContent);
    echo "✅ Variables de OpenAI agregadas al .env\n";
}

// Crear servicio de traducción
$translationServiceContent = '<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TranslationService
{
    private $apiKey;
    private $model;

    public function __construct()
    {
        $this->apiKey = config(\'services.openai.api_key\');
        $this->model = config(\'services.openai.model\', \'gpt-3.5-turbo\');
    }

    /**
     * Traduce texto del inglés al español chileno
     */
    public function translateToChileanSpanish(string $text, string $type = \'general\'): ?string
    {
        if (empty($text) || empty($this->apiKey)) {
            return null;
        }

        // Cache key basado en el texto y tipo
        $cacheKey = "translation_" . md5($text . $type);
        
        // Verificar si ya está en cache
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $prompt = $this->buildPrompt($text, $type);
            
            $response = Http::withHeaders([
                \'Authorization\' => \'Bearer \' . $this->apiKey,
                \'Content-Type\' => \'application/json\',
            ])->timeout(30)->post(\'https://api.openai.com/v1/chat/completions\', [
                \'model\' => $this->model,
                \'messages\' => [
                    [
                        \'role\' => \'system\',
                        \'content\' => \'Eres un traductor especializado en K-dramas y cultura coreana. Tu audiencia son fanáticas chilenas de los dramas coreanos. Traduce siempre manteniendo el contexto cultural y usando expresiones naturales del español chileno.\'
                    ],
                    [
                        \'role\' => \'user\',
                        \'content\' => $prompt
                    ]
                ],
                \'max_tokens\' => 1000,
                \'temperature\' => 0.3,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $translation = $result[\'choices\'][0][\'message\'][\'content\'] ?? null;
                
                if ($translation) {
                    // Guardar en cache por 30 días
                    Cache::put($cacheKey, $translation, now()->addDays(30));
                    
                    Log::info("Traducción exitosa", [
                        \'original\' => substr($text, 0, 100),
                        \'translated\' => substr($translation, 0, 100),
                        \'type\' => $type
                    ]);
                    
                    return $translation;
                }
            }

            Log::error("Error en traducción OpenAI", [
                \'response\' => $response->body(),
                \'status\' => $response->status()
            ]);

        } catch (\Exception $e) {
            Log::error("Excepción en traducción", [
                \'message\' => $e->getMessage(),
                \'text\' => substr($text, 0, 100)
            ]);
        }

        return null;
    }

    /**
     * Construye el prompt específico según el tipo de contenido
     */
    private function buildPrompt(string $text, string $type): string
    {
        $baseInstructions = "Traduce el siguiente texto del inglés al español, considerando que la audiencia son fanáticas chilenas de K-dramas. ";

        switch ($type) {
            case \'title\':
                return $baseInstructions . "Este es el título de un K-drama. Mantén el estilo dramático y atractivo. Si hay nombres propios coreanos, manténlos en su forma original.\n\nTexto: " . $text;
            
            case \'synopsis\':
            case \'overview\':
                return $baseInstructions . "Esta es la sinopsis de un K-drama. Usa un lenguaje emotivo y atractivo que genere interés. Mantén términos específicos de K-dramas si es necesario (como \'chaebol\', \'oppa\', etc.). Usa expresiones naturales del español chileno.\n\nTexto: " . $text;
            
            case \'tagline\':
                return $baseInstructions . "Este es un tagline o eslogan de un K-drama. Debe ser impactante y memorable.\n\nTexto: " . $text;
            
            case \'genre\':
                return $baseInstructions . "Este es el nombre de un género de K-drama. Traduce de manera estándar.\n\nTexto: " . $text;
            
            default:
                return $baseInstructions . "Traduce de manera natural y fluida.\n\nTexto: " . $text;
        }
    }

    /**
     * Traduce múltiples textos en batch para mayor eficiencia
     */
    public function translateBatch(array $texts, string $type = \'general\'): array
    {
        $results = [];
        
        foreach ($texts as $key => $text) {
            $results[$key] = $this->translateToChileanSpanish($text, $type);
        }
        
        return $results;
    }

    /**
     * Verifica si el servicio de traducción está disponible
     */
    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }
}';

file_put_contents('app/Services/TranslationService.php', $translationServiceContent);
echo "✅ Servicio de traducción creado en app/Services/TranslationService.php\n";

// Actualizar config/services.php
$servicesConfigPath = 'config/services.php';
$servicesConfig = file_get_contents($servicesConfigPath);

// Agregar configuración de OpenAI si no existe
if (!str_contains($servicesConfig, 'openai')) {
    $openaiConfig = "\n    'openai' => [\n        'api_key' => env('OPENAI_API_KEY'),\n        'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),\n    ],\n\n];";
    $servicesConfig = str_replace('];', $openaiConfig, $servicesConfig);
    file_put_contents($servicesConfigPath, $servicesConfig);
    echo "✅ Configuración de OpenAI agregada a config/services.php\n";
}

// Crear comando Artisan para traducir contenido existente
$commandContent = '<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Series;
use App\Services\TranslationService;

class TranslateExistingContent extends Command
{
    protected $signature = \'translate:content 
                            {--type=all : Tipo de contenido (titles, synopsis, all)}
                            {--limit=50 : Número de series a procesar por lote}
                            {--force : Forzar traducción aunque ya exista}\'
    ;
    
    protected $description = \'Traduce contenido existente de series al español chileno usando OpenAI\';

    public function handle()
    {
        $translationService = new TranslationService();
        
        if (!$translationService->isAvailable()) {
            $this->error(\'❌ Servicio de traducción no disponible. Verifica OPENAI_API_KEY en .env\');
            return Command::FAILURE;
        }

        $type = $this->option(\'type\');
        $limit = (int) $this->option(\'limit\');
        $force = $this->option(\'force\');

        $this->info("🚀 Iniciando traducción de contenido...");
        $this->info("📊 Tipo: {$type}, Límite: {$limit}, Forzar: " . ($force ? "Sí" : "No"));

        $query = Series::query();
        
        // Filtrar series que necesitan traducción
        if (!$force) {
            if ($type === \'titles\' || $type === \'all\') {
                $query->where(function($q) {
                    $q->whereNull(\'title_es\')->orWhere(\'title_es\', \'\');
                });
            }
            if ($type === \'synopsis\' || $type === \'all\') {
                $query->where(function($q) {
                    $q->whereNull(\'overview_es\')->orWhere(\'overview_es\', \'\');
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
                    if (($type === \'titles\' || $type === \'all\') && 
                        ($force || empty($serie->title_es))) {
                        
                        if ($serie->title) {
                            $translatedTitle = $translationService->translateToChileanSpanish(
                                $serie->title, 
                                \'title\'
                            );
                            
                            if ($translatedTitle) {
                                $serie->title_es = $translatedTitle;
                                $updated = true;
                            }
                        }
                    }

                    // Traducir sinopsis
                    if (($type === \'synopsis\' || $type === \'all\') && 
                        ($force || empty($serie->overview_es))) {
                        
                        if ($serie->overview) {
                            $translatedOverview = $translationService->translateToChileanSpanish(
                                $serie->overview, 
                                \'synopsis\'
                            );
                            
                            if ($translatedOverview) {
                                $serie->overview_es = $translatedOverview;
                                $updated = true;
                            }
                        }
                    }

                    // Traducir tagline
                    if (($type === \'all\') && 
                        ($force || empty($serie->tagline_es))) {
                        
                        if ($serie->tagline) {
                            $translatedTagline = $translationService->translateToChileanSpanish(
                                $serie->tagline, 
                                \'tagline\'
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
}';

file_put_contents('app/Console/Commands/TranslateExistingContent.php', $commandContent);
echo "✅ Comando de traducción creado en app/Console/Commands/TranslateExistingContent.php\n";

echo "\n🎯 Sistema de traducción configurado exitosamente!\n";
echo "\n📋 Próximos pasos:\n";
echo "1. Obtén tu API key de OpenAI en: https://platform.openai.com/api-keys\n";
echo "2. Actualiza OPENAI_API_KEY en tu archivo .env\n";
echo "3. Ejecuta: php artisan translate:content --type=all --limit=10\n";
echo "\n💡 Comandos disponibles:\n";
echo "- php artisan translate:content --type=titles    (solo títulos)\n";
echo "- php artisan translate:content --type=synopsis  (solo sinopsis)\n";
echo "- php artisan translate:content --type=all       (todo el contenido)\n";
echo "- php artisan translate:content --force          (re-traducir existentes)\n";