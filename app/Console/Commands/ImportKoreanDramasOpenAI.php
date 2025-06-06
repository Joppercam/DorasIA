<?php

namespace App\Console\Commands;

use App\Models\Series;
use App\Models\Season;
use App\Models\Episode;
use App\Models\Person;
use App\Models\Genre;
use App\Services\TmdbService;
use App\Services\TranslationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ImportKoreanDramasOpenAI extends Command
{
    protected $signature = 'import:korean-dramas-openai {--pages=5 : Number of pages to import} {--with-details : Import full details including episodes} {--clean : Clean existing data first}';
    protected $description = 'Import Korean dramas from TMDB with professional OpenAI translations to Spanish';

    private TmdbService $tmdb;
    private TranslationService $translator;
    private int $totalTranslated = 0;
    private int $totalImported = 0;

    public function __construct(TmdbService $tmdb, TranslationService $translator)
    {
        parent::__construct();
        $this->tmdb = $tmdb;
        $this->translator = $translator;
    }

    public function handle()
    {
        $this->info('🎭 DORASIA - Importación Profesional con OpenAI');
        $this->info('============================================');
        
        // Check if OpenAI service is available
        if (!$this->translator->isAvailable()) {
            $this->error('❌ Servicio de traducción OpenAI no disponible');
            $this->error('   Verifica que OPENAI_API_KEY esté configurado en .env');
            return Command::FAILURE;
        }
        
        $serviceInfo = $this->translator->getServiceInfo();
        $this->info("✅ OpenAI configurado: {$serviceInfo['model']}");
        if ($serviceInfo['has_organization']) {
            $this->info("   🏢 Organización: " . env('OPENAI_ORGANIZATION'));
        }
        $this->newLine();
        
        $pages = (int) $this->option('pages');
        $withDetails = $this->option('with-details');
        $clean = $this->option('clean');
        
        // Clean existing data if requested
        if ($clean) {
            $this->cleanExistingData();
        }
        
        // Test translation service first
        $this->testTranslationService();
        
        // Import genres first
        $this->importGenres();
        
        // Import K-dramas with translations
        $this->importKoreanDramas($pages, $withDetails);
        
        // Show final summary
        $this->showFinalSummary();
        
        return Command::SUCCESS;
    }

    private function testTranslationService(): void
    {
        $this->info('🧪 Probando servicio de traducción...');
        
        $testResults = $this->translator->testService();
        
        if ($testResults['overall_success']) {
            $this->info("   ✅ Servicio funcionando correctamente ({$testResults['duration_seconds']}s)");
            $this->line("   📝 Ejemplo: '{$testResults['test_results']['title']['original']}' → '{$testResults['test_results']['title']['translated']}'");
        } else {
            $this->warn('   ⚠️  Servicio con problemas, continuando con traducción básica');
        }
        $this->newLine();
    }

    private function cleanExistingData(): void
    {
        $this->warn('🧹 Limpiando datos existentes...');
        
        if (!$this->confirm('¿Estás seguro de eliminar todos los datos existentes?')) {
            $this->info('Operación cancelada');
            exit;
        }
        
        try {
            DB::statement('PRAGMA foreign_keys = OFF');
            
            $tablesToClean = [
                'series_person',
                'series_genre', 
                'seasons',
                'episodes',
                'series',
                'people',
                'genres'
            ];

            foreach ($tablesToClean as $table) {
                $count = DB::table($table)->count();
                if ($count > 0) {
                    DB::statement("DELETE FROM {$table}");
                    $this->line("   ✅ {$table}: {$count} registros eliminados");
                }
            }

            // Reset auto-increment
            $autoIncrementTables = ['series', 'seasons', 'episodes', 'people', 'genres'];
            foreach ($autoIncrementTables as $table) {
                DB::statement("DELETE FROM sqlite_sequence WHERE name='{$table}'");
            }

            DB::statement('PRAGMA foreign_keys = ON');
            $this->info('   ✅ Limpieza completada');
            $this->newLine();
            
        } catch (\Exception $e) {
            $this->error("❌ Error limpiando datos: " . $e->getMessage());
            exit(1);
        }
    }

    private function importGenres(): void
    {
        $this->info('📚 Importando géneros con traducciones...');
        
        $genres = $this->tmdb->getTvGenres();
        $genreCount = 0;
        $translatedCount = 0;
        
        if (!empty($genres['genres'])) {
            foreach ($genres['genres'] as $genreData) {
                $nameEs = $this->translator->translateGenre($genreData['name']);
                
                Genre::updateOrCreate(
                    ['tmdb_id' => $genreData['id']],
                    [
                        'name' => $genreData['name'],
                        'name_es' => $nameEs ?: $this->translateGenreManual($genreData['name'])
                    ]
                );
                
                $genreCount++;
                if ($nameEs) {
                    $translatedCount++;
                }
            }
        }
        
        $this->info("   ✅ {$genreCount} géneros importados ({$translatedCount} traducidos con OpenAI)");
        $this->newLine();
    }

    private function translateGenreManual(string $genre): string
    {
        $manualTranslations = [
            'Action' => 'Acción',
            'Adventure' => 'Aventura',
            'Animation' => 'Animación',
            'Comedy' => 'Comedia',
            'Crime' => 'Crimen',
            'Documentary' => 'Documental',
            'Drama' => 'Drama',
            'Family' => 'Familia',
            'Fantasy' => 'Fantasía',
            'History' => 'Historia',
            'Horror' => 'Terror',
            'Music' => 'Música',
            'Mystery' => 'Misterio',
            'Romance' => 'Romance',
            'Science Fiction' => 'Ciencia Ficción',
            'Thriller' => 'Suspenso',
            'War' => 'Guerra',
            'Western' => 'Western',
            'Soap' => 'Telenovela',
            'Talk' => 'Programa de Entrevistas',
            'War & Politics' => 'Guerra y Política',
            'Kids' => 'Infantil'
        ];

        return $manualTranslations[$genre] ?? $genre;
    }

    private function importKoreanDramas(int $pages, bool $withDetails): void
    {
        $this->info("📺 Importando K-Dramas con OpenAI ({$pages} páginas)...");
        
        for ($page = 1; $page <= $pages; $page++) {
            $this->line("   📄 Procesando página {$page}/{$pages}...");
            
            $dramas = $this->tmdb->getPopularKoreanDramas($page);
            
            if (empty($dramas['results'])) {
                $this->warn("     ⚠️  Sin resultados en página {$page}");
                continue;
            }
            
            foreach ($dramas['results'] as $dramaData) {
                try {
                    $series = $this->importSeriesWithTranslation($dramaData);
                    if ($series) {
                        $this->line("     ✅ {$series->display_title}");
                        
                        if ($withDetails) {
                            $this->importSeriesDetails($series);
                        }
                        
                        $this->totalImported++;
                    }
                    
                } catch (\Exception $e) {
                    $this->error("     ❌ Error en {$dramaData['name']}: " . $e->getMessage());
                    Log::error("Import error", [
                        'drama' => $dramaData['name'],
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Small delay between pages
            if ($page < $pages) {
                sleep(1);
            }
        }
    }

    private function importSeriesWithTranslation(array $dramaData): ?Series
    {
        // Check if already exists
        $existingSeries = Series::where('tmdb_id', $dramaData['id'])->first();
        if ($existingSeries) {
            return $existingSeries;
        }
        
        $title = $dramaData['name'];
        $overview = $dramaData['overview'] ?? null;
        
        // Try OpenAI first, fallback to specific known translations only
        $titleEs = null;
        $overviewEs = null;
        
        if ($title) {
            $titleEs = $this->translator->translateTitle($title);
            if ($titleEs) {
                $this->totalTranslated++;
            } else {
                // Only use known exact translations, otherwise keep original
                $titleEs = $this->getKnownTranslation($title) ?: $title;
            }
        }
        
        if ($overview) {
            $overviewEs = $this->translator->translateSynopsis($overview);
            if ($overviewEs) {
                $this->totalTranslated++;
            } else {
                // For descriptions, only translate if it's a known complete phrase
                $overviewEs = $this->getKnownTranslation($overview) ?: $overview;
            }
        }
        
        $series = Series::create([
            'title' => $title,
            'title_es' => $titleEs,
            'original_title' => $dramaData['original_name'] ?? $title,
            'overview' => $overview,
            'overview_es' => $overviewEs,
            'first_air_date' => $dramaData['first_air_date'] ?? null,
            'vote_average' => $dramaData['vote_average'] ?? 0,
            'vote_count' => $dramaData['vote_count'] ?? 0,
            'popularity' => $dramaData['popularity'] ?? 0,
            'poster_path' => $dramaData['poster_path'] ?? null,
            'backdrop_path' => $dramaData['backdrop_path'] ?? null,
            'original_language' => $dramaData['original_language'] ?? 'ko',
            'origin_country' => is_array($dramaData['origin_country'] ?? []) ? 
                implode(',', $dramaData['origin_country']) : 'KR',
            'tmdb_id' => $dramaData['id'],
            'is_korean_drama' => true
        ]);
        
        // Attach genres
        if (!empty($dramaData['genre_ids'])) {
            $genreIds = Genre::whereIn('tmdb_id', $dramaData['genre_ids'])->pluck('id');
            $series->genres()->attach($genreIds);
        }
        
        return $series;
    }

    private function importSeriesDetails(Series $series): void
    {
        $details = $this->tmdb->getTvShowDetails($series->tmdb_id);
        
        if (empty($details)) {
            return;
        }
        
        // Translate additional content
        $synopsis = $details['overview'] ?? $series->overview;
        $tagline = $details['tagline'] ?? null;
        
        $synopsisEs = null;
        $taglineEs = null;
        
        if ($synopsis && $synopsis !== $series->overview) {
            $synopsisEs = $this->translator->translateSynopsis($synopsis);
            if ($synopsisEs) {
                $this->totalTranslated++;
            }
        }
        
        if ($tagline) {
            $taglineEs = $this->translator->translateToChileanSpanish($tagline, 'tagline');
            if ($taglineEs) {
                $this->totalTranslated++;
            }
        }
        
        // Update series with detailed information
        $series->update([
            'synopsis' => $synopsis,
            'synopsis_es' => $synopsisEs,
            'tagline' => $tagline,
            'tagline_es' => $taglineEs,
            'status' => $details['status'] ?? null,
            'last_air_date' => $details['last_air_date'] ?? null,
            'number_of_seasons' => $details['number_of_seasons'] ?? 0,
            'number_of_episodes' => $details['number_of_episodes'] ?? 0,
            'episode_run_time' => !empty($details['episode_run_time']) ? 
                implode(',', $details['episode_run_time']) : null,
            'production_companies' => !empty($details['production_companies']) ? 
                json_encode($details['production_companies']) : null,
            'production_countries' => !empty($details['production_countries']) ? 
                json_encode($details['production_countries']) : null,
            'spoken_languages' => !empty($details['spoken_languages']) ? 
                json_encode($details['spoken_languages']) : null,
            'networks' => !empty($details['networks']) ? 
                json_encode($details['networks']) : null,
            'homepage' => $details['homepage'] ?? null,
            'in_production' => $details['in_production'] ?? false,
            'type' => $details['type'] ?? null
        ]);
    }

    private function showFinalSummary(): void
    {
        $this->newLine();
        $this->info('🎉 Importación completada con éxito!');
        $this->info('=====================================');
        
        try {
            $seriesCount = Series::count();
            $peopleCount = Person::count();
            $genresCount = Genre::count();
            $translatedTitles = Series::whereNotNull('title_es')->count();
            $translatedOverviews = Series::whereNotNull('overview_es')->count();

            $this->table(['Estadística', 'Cantidad'], [
                ['📺 Total series', $seriesCount],
                ['👥 Total personas', $peopleCount],
                ['🎭 Total géneros', $genresCount],
                ['🇪🇸 Títulos en español', $translatedTitles],
                ['📝 Sinopsis en español', $translatedOverviews],
                ['🤖 Traducciones OpenAI', $this->totalTranslated],
                ['📊 Series importadas esta vez', $this->totalImported]
            ]);
            
            if ($seriesCount > 0) {
                $translationPercentage = round(($translatedTitles / $seriesCount) * 100, 1);
                $this->info("📊 Porcentaje de traducciones: {$translationPercentage}%");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error obteniendo estadísticas: " . $e->getMessage());
        }
        
        $this->newLine();
        $this->info('🚀 Próximos pasos:');
        $this->line('   1. Verificar contenido en https://dorasia.cl');
        $this->line('   2. Para deployment: git add . && git commit -m "Importación con OpenAI" && git push');
        $this->line('   3. En hosting: git pull && php84 artisan import:korean-dramas-openai --clean --pages=20');
        $this->newLine();
        
        $serviceInfo = $this->translator->getServiceInfo();
        $this->info("💡 Servicio OpenAI usado: {$serviceInfo['model']} (versión {$serviceInfo['version']})");
    }

    private function getKnownTranslation(string $text): ?string
    {
        // Only exact translations for known K-drama titles and complete phrases
        $knownTranslations = [
            // Specific K-drama translations based on popular series
            'Squid Game' => 'El Juego del Calamar',
            'The Return of Superman' => 'El Regreso de Superman',
            'Running Man' => 'Running Man',
            'Meant to Be' => 'Destinados a Estar Juntos',
            'Crash Landing on You' => 'Aterrizaje de Emergencia en Tu Corazón',
            'Descendants of the Sun' => 'Descendientes del Sol',
            'Goblin' => 'Goblin',
            'Boys Over Flowers' => 'Chicos Antes que Flores',
            'My Love from the Star' => 'Mi Amor de las Estrellas',
            'Coffee Prince' => 'El Príncipe del Café',
            'Winter Sonata' => 'Sonata de Invierno',
            'Full House' => 'Casa Llena',
            'City Hunter' => 'Cazador de la Ciudad',
            'Secret Garden' => 'Jardín Secreto',
            'My ID is Gangnam Beauty' => 'Mi ID es Belleza de Gangnam',
            'What\'s Wrong with Secretary Kim' => 'Qué Pasa con la Secretaria Kim',
            'Strong Woman Do Bong Soon' => 'Mujer Fuerte Do Bong Soon',
            'Hotel del Luna' => 'Hotel del Luna',
            'Itaewon Class' => 'Clase Itaewon',
            'Kingdom' => 'Reino',
            'Sweet Home' => 'Dulce Hogar',
            'Reply 1988' => 'Respuesta 1988',
            'Sky Castle' => 'Castillo en el Cielo',
            'Prison Playbook' => 'Manual de la Prisión',
            'Hospital Playlist' => 'Lista de Reproducción del Hospital',
            'When the Camellia Blooms' => 'Cuando Florecen las Camelias',
            'The Penthouse' => 'El Penthouse',
            'Vincenzo' => 'Vincenzo',
            'True Beauty' => 'Belleza Verdadera',
            'Start-Up' => 'Start-Up',
            'Nevertheless' => 'Sin Embargo',
            'Hometown\'s Embrace' => 'El Abrazo del Hogar',
            'Youth of May' => 'Juventud de Mayo',
            'Move to Heaven' => 'Mudanza al Cielo',
            'Mine' => 'Mío',
            'The Devil Judge' => 'El Juez Diablo',
            'Hospital Ship' => 'Barco Hospital',
            'Weightlifting Fairy Kim Bok-joo' => 'El Hada de las Pesas Kim Bok-joo',
            'Love Alarm' => 'Alarma de Amor',
            'Record of Youth' => 'Registro de la Juventud',
            'The King: Eternal Monarch' => 'El Rey: Monarca Eterno',
            'It\'s Okay to Not Be Okay' => 'Está Bien No Estar Bien',
            'Flower of Evil' => 'Flor del Mal',
            'Do You Like Brahms?' => '¿Te Gusta Brahms?',
            'Tale of the Nine Tailed' => 'Historia de las Nueve Colas',
            'Mr. Queen' => 'Sr. Reina',
            'True Beauty' => 'Belleza Verdadera',
            'The Uncanny Counter' => 'El Contador Misterioso',
            'Sweet Magnolias' => 'Magnolias Dulces',
            'Love in the Moonlight' => 'Amor a la Luz de la Luna',
            'Scarlet Heart: Ryeo' => 'Corazón Escarlata: Ryeo',
            'Cheese in the Trap' => 'Queso en la Trampa',
            'W: Two Worlds' => 'W: Dos Mundos',
            'Doctors' => 'Doctores',
            'Moon Lovers' => 'Amantes de la Luna',
            'She Was Pretty' => 'Ella Era Bonita',
            'Kill Me, Heal Me' => 'Mátame, Cúrame',
            'Healer' => 'Sanador',
            'Pinocchio' => 'Pinocho',
            'You Who Came from the Stars' => 'Tú Que Viniste de las Estrellas',
            'The Heirs' => 'Los Herederos',
            'Master\'s Sun' => 'El Sol del Maestro',
            'Gu Family Book' => 'El Libro de la Familia Gu',
            'That Winter, The Wind Blows' => 'Ese Invierno, Sopla el Viento',
            'School 2013' => 'Escuela 2013',
            'Faith' => 'Fe',
            'Nice Guy' => 'Buen Chico',
            'King 2 Hearts' => 'Rey 2 Corazones',
            'Dream High' => 'Sueño Alto',
            'Mary Stayed Out All Night' => 'Mary Se Quedó Fuera Toda la Noche',
            'Playful Kiss' => 'Beso Juguetón',
            'Personal Taste' => 'Gusto Personal',
            'You\'re Beautiful' => 'Eres Hermoso',
            'Shining Inheritance' => 'Herencia Brillante',
            
            // Complete known descriptions for popular shows
            'Hundreds of cash-strapped players accept a strange invitation to compete in children\'s games. Inside, a tempting prize awaits — with deadly high stakes.' => 'Cientos de jugadores sin dinero aceptan una extraña invitación para competir en juegos infantiles. Adentro, un premio tentador los espera — con riesgos mortales.',
            
            'A tragic story of a father and daughter begins when his greed turns them into sworn enemies.' => 'Una historia trágica de un padre y una hija comienza cuando la codicia de él los convierte en enemigos jurados.',
            
            'An up-close look at celebrity dads who have to take care of their kids for the next 48 hours without any help from their wives. Will it be a dad-tastrophe, or can they finish their wives\' to-do lists while having fun with their kids?' => 'Una mirada íntima a papás famosos que tienen que cuidar a sus hijos durante las próximas 48 horas sin ayuda de sus esposas. ¿Será una catástrofe paterna, o podrán completar las listas de tareas de sus esposas mientras se divierten con sus hijos?',
            
            'These days, as variety shows are gradually disappearing, this is one of Korea\'s representative variety shows.' => 'En estos días, mientras los programas de variedades van desapareciendo gradualmente, este es uno de los programas de variedades más representativos de Corea.',
        ];

        return $knownTranslations[$text] ?? null;
    }
}