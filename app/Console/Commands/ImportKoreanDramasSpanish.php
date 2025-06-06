<?php

namespace App\Console\Commands;

use App\Models\Series;
use App\Models\Season;
use App\Models\Episode;
use App\Models\Person;
use App\Models\Genre;
use App\Models\Image;
use App\Models\Soundtrack;
use App\Services\TmdbService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImportKoreanDramasSpanish extends Command
{
    protected $signature = 'import:korean-dramas-spanish {--pages=5 : Number of pages to import} {--with-details : Import full details including episodes}';
    protected $description = 'Import Korean dramas from TMDB with Spanish translations';

    private TmdbService $tmdb;
    private array $translations;

    public function __construct(TmdbService $tmdb)
    {
        parent::__construct();
        $this->tmdb = $tmdb;
        $this->initializeTranslations();
    }

    private function initializeTranslations()
    {
        $this->translations = [
            // Títulos comunes de K-Dramas
            'Love' => 'Amor',
            'Love Story' => 'Historia de Amor',
            'True Love' => 'Amor Verdadero',
            'First Love' => 'Primer Amor',
            'Secret Love' => 'Amor Secreto',
            'Pure Love' => 'Amor Puro',
            'Sweet Love' => 'Amor Dulce',
            'Lost Love' => 'Amor Perdido',
            'Heart' => 'Corazón',
            'Secret' => 'Secreto',
            'Beautiful' => 'Hermoso',
            'Beauty' => 'Belleza',
            'Beast' => 'Bestia',
            'Dream' => 'Sueño',
            'Princess' => 'Princesa',
            'Prince' => 'Príncipe',
            'King' => 'Rey',
            'Queen' => 'Reina',
            'Emperor' => 'Emperador',
            'Empress' => 'Emperatriz',
            'Doctor' => 'Doctor',
            'Hospital' => 'Hospital',
            'School' => 'Escuela',
            'High School' => 'Preparatoria',
            'University' => 'Universidad',
            'College' => 'Universidad',
            'Romance' => 'Romance',
            'Romantic' => 'Romántico',
            'Story' => 'Historia',
            'Tale' => 'Cuento',
            'Legend' => 'Leyenda',
            'Life' => 'Vida',
            'Time' => 'Tiempo',
            'City' => 'Ciudad',
            'Night' => 'Noche',
            'Day' => 'Día',
            'Moon' => 'Luna',
            'Sun' => 'Sol',
            'Star' => 'Estrella',
            'Sky' => 'Cielo',
            'Heaven' => 'Cielo',
            'Spring' => 'Primavera',
            'Summer' => 'Verano',
            'Winter' => 'Invierno',
            'Autumn' => 'Otoño',
            'Family' => 'Familia',
            'Friend' => 'Amigo',
            'Friends' => 'Amigos',
            'Home' => 'Hogar',
            'House' => 'Casa',
            'Hotel' => 'Hotel',
            'Cafe' => 'Café',
            'Coffee' => 'Café',
            'Restaurant' => 'Restaurante',
            'Office' => 'Oficina',
            'Company' => 'Empresa',
            'Business' => 'Negocio',
            'Marriage' => 'Matrimonio',
            'Wedding' => 'Boda',
            'Proposal' => 'Propuesta',
            'Reply' => 'Respuesta',
            'Answer' => 'Respuesta',
            'Signal' => 'Señal',
            'Memory' => 'Memoria',
            'Memories' => 'Memorias',
            'Youth' => 'Juventud',
            'Young' => 'Joven',
            'Age' => 'Edad',
            'Again' => 'De Nuevo',
            'Return' => 'Regreso',
            'Come Back' => 'Regresa',
            'Comeback' => 'Regreso',
            'Goodbye' => 'Adiós',
            'Hello' => 'Hola',
            'Miss' => 'Señorita',
            'Mister' => 'Señor',
            'Strong' => 'Fuerte',
            'Weak' => 'Débil',
            'Lucky' => 'Afortunado',
            'Happy' => 'Feliz',
            'Happiness' => 'Felicidad',
            'Sad' => 'Triste',
            'Tears' => 'Lágrimas',
            'Cry' => 'Llorar',
            'Smile' => 'Sonrisa',
            'Laugh' => 'Reír',
            'Angry' => 'Enojado',
            'Crazy' => 'Loco',
            'Mad' => 'Loco',
            'Sweet' => 'Dulce',
            'Bitter' => 'Amargo',
            'Hot' => 'Caliente',
            'Cold' => 'Frío',
            'Fire' => 'Fuego',
            'Water' => 'Agua',
            'World' => 'Mundo',
            'Universe' => 'Universo',
            'Space' => 'Espacio',
            'Magic' => 'Magia',
            'Magical' => 'Mágico',
            'Miracle' => 'Milagro',
            'Angel' => 'Ángel',
            'Devil' => 'Diablo',
            'God' => 'Dios',
            'Spirit' => 'Espíritu',
            'Soul' => 'Alma',
            'Ghost' => 'Fantasma',
            'Monster' => 'Monstruo',
            'Hero' => 'Héroe',
            'Heroine' => 'Heroína',
            'Villain' => 'Villano',
            'Fighter' => 'Luchador',
            'Warrior' => 'Guerrero',
            'Soldier' => 'Soldado',
            'Detective' => 'Detective',
            'Police' => 'Policía',
            'Lawyer' => 'Abogado',
            'Judge' => 'Juez',
            'Teacher' => 'Profesor',
            'Student' => 'Estudiante',
            'Chef' => 'Chef',
            'Cook' => 'Cocinero',
            'Artist' => 'Artista',
            'Singer' => 'Cantante',
            'Actor' => 'Actor',
            'Actress' => 'Actriz',
            'Model' => 'Modelo',
            'Fashion' => 'Moda',
            'Style' => 'Estilo',
            'Flower' => 'Flor',
            'Garden' => 'Jardín',
            'Forest' => 'Bosque',
            'Mountain' => 'Montaña',
            'River' => 'Río',
            'Sea' => 'Mar',
            'Ocean' => 'Océano',
            'Island' => 'Isla',
            'Beach' => 'Playa',
            'Park' => 'Parque',
            'Street' => 'Calle',
            'Road' => 'Camino',
            'Bridge' => 'Puente',
            'Tower' => 'Torre',
            'Castle' => 'Castillo',
            'Palace' => 'Palacio',
            'Temple' => 'Templo',
            'Church' => 'Iglesia',
            'Market' => 'Mercado',
            'Shop' => 'Tienda',
            'Store' => 'Tienda',
            'Bank' => 'Banco',
            'Station' => 'Estación',
            'Airport' => 'Aeropuerto',
            'Train' => 'Tren',
            'Bus' => 'Autobús',
            'Car' => 'Auto',
            'Walk' => 'Caminar',
            'Run' => 'Correr',
            'Dance' => 'Bailar',
            'Sing' => 'Cantar',
            'Play' => 'Jugar',
            'Game' => 'Juego',
            'Sport' => 'Deporte',
            'Team' => 'Equipo',
            'Match' => 'Partido',
            'Win' => 'Ganar',
            'Victory' => 'Victoria',
            'Lose' => 'Perder',
            'Fight' => 'Pelear',
            'War' => 'Guerra',
            'Peace' => 'Paz',
            'Freedom' => 'Libertad',
            'Justice' => 'Justicia',
            'Truth' => 'Verdad',
            'Lie' => 'Mentira',
            'Hope' => 'Esperanza',
            'Faith' => 'Fe',
            'Destiny' => 'Destino',
            'Fate' => 'Destino',
            'Chance' => 'Oportunidad',
            'Lucky' => 'Suerte',
            'Miracle' => 'Milagro',
            'Promise' => 'Promesa',
            'Forever' => 'Para Siempre',
            'Always' => 'Siempre',
            'Never' => 'Nunca',
            'Tomorrow' => 'Mañana',
            'Yesterday' => 'Ayer',
            'Today' => 'Hoy',
            'Tonight' => 'Esta Noche',
            'Morning' => 'Mañana',
            'Evening' => 'Tarde',
            'Midnight' => 'Medianoche',
            'Dawn' => 'Amanecer',
            'Sunset' => 'Atardecer',
            'Sunrise' => 'Amanecer',
            'Holiday' => 'Vacaciones',
            'Journey' => 'Viaje',
            'Adventure' => 'Aventura',
            'Mystery' => 'Misterio',
            'Secret Garden' => 'Jardín Secreto',
            'Golden' => 'Dorado',
            'Silver' => 'Plateado',
            'Diamond' => 'Diamante',
            'Pearl' => 'Perla',
            'Ruby' => 'Rubí',
            'Emerald' => 'Esmeralda',
            'Rainbow' => 'Arcoíris',
            'Thunder' => 'Trueno',
            'Lightning' => 'Rayo',
            'Storm' => 'Tormenta',
            'Snow' => 'Nieve',
            'Rain' => 'Lluvia',
            'Wind' => 'Viento',
            'Cloud' => 'Nube',
            'Shadow' => 'Sombra',
            'Light' => 'Luz',
            'Dark' => 'Oscuro',
            'Darkness' => 'Oscuridad',
            'Bright' => 'Brillante',
            'Shine' => 'Brillar',
            'Glow' => 'Resplandor',
            'Spark' => 'Chispa',
            'Flame' => 'Llama',
            'Ice' => 'Hielo',
            'Crystal' => 'Cristal',
            'Glass' => 'Vidrio',
            'Mirror' => 'Espejo',
            'Window' => 'Ventana',
            'Door' => 'Puerta',
            'Key' => 'Llave',
            'Lock' => 'Cerradura',
            'Open' => 'Abrir',
            'Close' => 'Cerrar',
            'Enter' => 'Entrar',
            'Exit' => 'Salir',
            'Start' => 'Comenzar',
            'End' => 'Final',
            'Beginning' => 'Comienzo',
            'Finish' => 'Terminar',
            'Complete' => 'Completo',
            'Perfect' => 'Perfecto',
            'Imperfect' => 'Imperfecto',
            'Simple' => 'Simple',
            'Complex' => 'Complejo',
            'Easy' => 'Fácil',
            'Hard' => 'Difícil',
            'Difficult' => 'Difícil',
            'Impossible' => 'Imposible',
            'Possible' => 'Posible',
            'Real' => 'Real',
            'Fake' => 'Falso',
            'True' => 'Verdadero',
            'False' => 'Falso'
        ];
    }

    private function translateText(string $text): string
    {
        if (empty($text)) {
            return $text;
        }

        $result = $text;
        
        // Traducir frases completas primero (más específicas)
        foreach ($this->translations as $english => $spanish) {
            if (strlen($english) > 10) { // Frases largas primero
                $pattern = '/\b' . preg_quote($english, '/') . '\b/i';
                $result = preg_replace($pattern, $spanish, $result);
            }
        }
        
        // Luego palabras individuales
        foreach ($this->translations as $english => $spanish) {
            if (strlen($english) <= 10) {
                $pattern = '/\b' . preg_quote($english, '/') . '\b/i';
                $result = preg_replace($pattern, $spanish, $result);
            }
        }
        
        return $result;
    }

    public function handle()
    {
        $this->info('🎭 Starting Korean Drama import from TMDB with Spanish translations...');
        
        $pages = (int) $this->option('pages');
        $withDetails = $this->option('with-details');
        
        // First, import genres
        $this->importGenres();
        
        $totalImported = 0;
        
        for ($page = 1; $page <= $pages; $page++) {
            $this->info("📄 Processing page {$page} of {$pages}...");
            
            // Get popular Korean dramas
            $dramas = $this->tmdb->getPopularKoreanDramas($page);
            
            if (empty($dramas['results'])) {
                $this->warn("No results found for page {$page}");
                continue;
            }
            
            foreach ($dramas['results'] as $dramaData) {
                try {
                    $series = $this->importSeries($dramaData);
                    if ($series) {
                        $this->info("✅ Imported: {$series->display_title}");
                        if ($withDetails) {
                            $this->importSeriesDetails($series);
                        }
                        $totalImported++;
                    }
                } catch (\Exception $e) {
                    $this->error("❌ Error importing {$dramaData['name']}: " . $e->getMessage());
                    Log::error("Import error for {$dramaData['name']}: " . $e->getMessage());
                }
            }
            
            // Small delay to be respectful to the API
            sleep(1);
        }
        
        $this->info("🎉 Import completed! Total imported: {$totalImported} dramas");
        
        return Command::SUCCESS;
    }

    private function importGenres(): void
    {
        $this->info('📚 Importing genres...');
        
        $genres = $this->tmdb->getTvGenres();
        
        foreach ($genres['genres'] as $genreData) {
            Genre::updateOrCreate(
                ['tmdb_id' => $genreData['id']],
                [
                    'name' => $genreData['name'],
                    'name_es' => $this->translateGenre($genreData['name'])
                ]
            );
        }
        
        $this->info('✅ Genres imported successfully');
    }

    private function translateGenre(string $genre): string
    {
        $genreTranslations = [
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
            'News' => 'Noticias',
            'Reality' => 'Reality',
            'Romance' => 'Romance',
            'Science Fiction' => 'Ciencia Ficción',
            'TV Movie' => 'Película para TV',
            'Thriller' => 'Suspenso',
            'War' => 'Guerra',
            'Western' => 'Western',
            'Soap' => 'Telenovela',
            'Talk' => 'Programa de Entrevistas',
            'War & Politics' => 'Guerra y Política',
            'Kids' => 'Infantil'
        ];

        return $genreTranslations[$genre] ?? $genre;
    }

    private function importSeries(array $dramaData): ?Series
    {
        // Check if already exists
        $existingSeries = Series::where('tmdb_id', $dramaData['id'])->first();
        if ($existingSeries) {
            return $existingSeries;
        }
        
        // Translate title and overview
        $title = $dramaData['name'];
        $overview = $dramaData['overview'] ?? null;
        
        $titleEs = $this->translateText($title);
        $overviewEs = $overview ? $this->translateText($overview) : null;
        
        $series = Series::create([
            'title' => $title,
            'title_es' => $titleEs !== $title ? $titleEs : null,
            'original_title' => $dramaData['original_name'] ?? $title,
            'overview' => $overview,
            'overview_es' => $overviewEs && $overviewEs !== $overview ? $overviewEs : null,
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
        $this->line("🔍 Getting detailed info for: {$series->display_title}");
        
        $details = $this->tmdb->getTvShowDetails($series->tmdb_id);
        
        if (empty($details)) {
            return;
        }
        
        // Translate additional content
        $synopsis = $details['overview'] ?? $series->overview;
        $tagline = $details['tagline'] ?? null;
        
        $synopsisEs = $synopsis ? $this->translateText($synopsis) : null;
        $taglineEs = $tagline ? $this->translateText($tagline) : null;
        
        // Update series with detailed information
        $series->update([
            'synopsis' => $synopsis,
            'synopsis_es' => $synopsisEs && $synopsisEs !== $synopsis ? $synopsisEs : null,
            'tagline' => $tagline,
            'tagline_es' => $taglineEs && $taglineEs !== $tagline ? $taglineEs : null,
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

        // Import cast and crew
        $this->importSeriesPeople($series);
    }

    private function importSeriesPeople(Series $series): void
    {
        $credits = $this->tmdb->getTvShowCredits($series->tmdb_id);
        
        if (empty($credits)) {
            return;
        }

        // Import cast
        if (!empty($credits['cast'])) {
            foreach (array_slice($credits['cast'], 0, 10) as $castMember) {
                $person = $this->importPerson($castMember);
                if ($person) {
                    $series->people()->attach($person->id, [
                        'role' => 'actor',
                        'character' => $castMember['character'] ?? null,
                        'order' => $castMember['order'] ?? 0,
                        'department' => 'Acting',
                        'job' => 'Acting'
                    ]);
                }
            }
        }

        // Import crew (directors, writers)
        if (!empty($credits['crew'])) {
            $importantJobs = ['Director', 'Writer', 'Creator', 'Executive Producer'];
            foreach ($credits['crew'] as $crewMember) {
                if (in_array($crewMember['job'] ?? '', $importantJobs)) {
                    $person = $this->importPerson($crewMember);
                    if ($person) {
                        $series->people()->attach($person->id, [
                            'role' => strtolower($crewMember['job']),
                            'character' => null,
                            'order' => 0,
                            'department' => $crewMember['department'] ?? '',
                            'job' => $crewMember['job'] ?? ''
                        ]);
                    }
                }
            }
        }
    }

    private function importPerson(array $personData): ?Person
    {
        $existingPerson = Person::where('tmdb_id', $personData['id'])->first();
        if ($existingPerson) {
            return $existingPerson;
        }

        return Person::create([
            'name' => $personData['name'],
            'profile_path' => $personData['profile_path'] ?? null,
            'tmdb_id' => $personData['id'],
            'known_for_department' => $personData['known_for_department'] ?? null,
            'gender' => $personData['gender'] ?? 0,
            'popularity' => $personData['popularity'] ?? 0
        ]);
    }
}