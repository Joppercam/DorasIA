<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Episode;
use App\Models\Genre;
use App\Models\Person;
use App\Models\Season;
use App\Models\Title;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeedSampleTitles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:sample-titles {--type=all : Tipo de contenido a generar (all, k-drama, j-drama, c-drama, movie)} {--count=3 : Número de títulos a generar por categoría}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera títulos de muestra para el catálogo de Dorasia sin necesidad de API key externa';

    /**
     * Sample Korean drama data
     */
    protected $kDramas = [
        [
            'title' => 'Mi destino',
            'original_title' => '운명의 길',
            'synopsis' => 'Una joven doctora descubre que está conectada con un detective por eventos del pasado. Juntos resuelven misterios mientras enfrentan sus propios destinos entrelazados.',
            'release_year' => 2023,
            'genres' => ['Romance', 'Drama', 'Misterio'],
            'number_of_seasons' => 2,
            'episodes_per_season' => [16, 12],
            'poster' => 'storage/posters/poster-1.jpg',
            'backdrop' => 'storage/backdrops/backdrop-1.jpg',
        ],
        [
            'title' => 'Secretos de la Corona',
            'original_title' => '왕관의 비밀',
            'synopsis' => 'Ambientada en la dinastía Joseon, narra las intrigas palaciegas y el romance prohibido entre una dama de la corte y el príncipe heredero.',
            'release_year' => 2022,
            'genres' => ['Histórico', 'Romance', 'Drama'],
            'number_of_seasons' => 1,
            'episodes_per_season' => [20],
            'poster' => 'storage/posters/poster-2.jpg',
            'backdrop' => 'storage/backdrops/backdrop-2.jpg',
        ],
        [
            'title' => 'Coffee Dreams',
            'original_title' => '커피 드림즈',
            'synopsis' => 'Un barista talentoso pero tímido se enamora de una exitosa ejecutiva que frecuenta su cafetería. Una dulce historia sobre perseguir sueños y encontrar el amor.',
            'release_year' => 2023,
            'genres' => ['Romance', 'Comedia', 'Vida cotidiana'],
            'number_of_seasons' => 1,
            'episodes_per_season' => [16],
            'poster' => 'storage/posters/poster-3.jpg',
            'backdrop' => 'storage/backdrops/backdrop-3.jpg',
        ],
        [
            'title' => 'El último guardián',
            'original_title' => '마지막 수호자',
            'synopsis' => 'Un policía con habilidades sobrenaturales protege a Seúl de amenazas misteriosas mientras busca respuestas sobre su propio pasado.',
            'release_year' => 2021,
            'genres' => ['Acción', 'Fantasía', 'Thriller'],
            'number_of_seasons' => 2,
            'episodes_per_season' => [12, 10],
            'poster' => 'storage/posters/poster-4.jpg',
            'backdrop' => 'storage/backdrops/backdrop-4.jpg',
        ],
        [
            'title' => 'Memorias de Hospital',
            'original_title' => '병원의 기억',
            'synopsis' => 'Sigue la vida de médicos residentes en un prestigioso hospital universitario, enfrentando desafíos profesionales y personales mientras salvan vidas.',
            'release_year' => 2022,
            'genres' => ['Médico', 'Drama', 'Romance'],
            'number_of_seasons' => 1,
            'episodes_per_season' => [16],
            'poster' => 'storage/posters/poster-5.jpg',
            'backdrop' => 'storage/backdrops/backdrop-5.jpg',
        ],
    ];

    /**
     * Sample Japanese drama data
     */
    protected $jDramas = [
        [
            'title' => 'Corazones de Tokyo',
            'original_title' => '東京の心',
            'synopsis' => 'Cuatro amigos navegan la vida adulta en el bullicioso Tokyo, cada uno persiguiendo sus sueños mientras enfrentan los altibajos del amor y la carrera profesional.',
            'release_year' => 2023,
            'genres' => ['Drama', 'Vida cotidiana', 'Romance'],
            'number_of_seasons' => 1,
            'episodes_per_season' => [11],
            'poster' => 'storage/posters/poster-6.jpg',
            'backdrop' => 'storage/backdrops/backdrop-1.jpg',
        ],
        [
            'title' => 'El Chef Samurai',
            'original_title' => '侍シェフ',
            'synopsis' => 'Un talentoso chef con pasado misterioso utiliza técnicas culinarias de otro nivel para resolver conflictos y sanar corazones a través de la comida.',
            'release_year' => 2022,
            'genres' => ['Comedia', 'Drama', 'Misterio'],
            'number_of_seasons' => 1,
            'episodes_per_season' => [10],
            'poster' => 'storage/posters/poster-7.jpg',
            'backdrop' => 'storage/backdrops/backdrop-2.jpg',
        ],
        [
            'title' => 'Detectives del Tiempo',
            'original_title' => '時間の探偵',
            'synopsis' => 'Una policía moderna y un detective del período Meiji que viaja en el tiempo unen fuerzas para resolver casos que trascienden épocas.',
            'release_year' => 2023,
            'genres' => ['Misterio', 'Fantasía', 'Histórico'],
            'number_of_seasons' => 1,
            'episodes_per_season' => [12],
            'poster' => 'storage/posters/poster-8.jpg',
            'backdrop' => 'storage/backdrops/backdrop-3.jpg',
        ],
        [
            'title' => 'Melodía de Primavera',
            'original_title' => '春のメロディー',
            'synopsis' => 'Un pianista que perdió la pasión por la música conoce a una violinista autodidacta que le enseña a escuchar el mundo de una forma nueva.',
            'release_year' => 2021,
            'genres' => ['Romance', 'Drama', 'Música'],
            'number_of_seasons' => 1,
            'episodes_per_season' => [10],
            'poster' => 'storage/posters/poster-9.jpg',
            'backdrop' => 'storage/backdrops/backdrop-4.jpg',
        ],
        [
            'title' => 'Secretos de Familia',
            'original_title' => '家族の秘密',
            'synopsis' => 'Una familia aparentemente perfecta comienza a desmoronarse cuando un antiguo secreto sale a la luz, poniendo a prueba los lazos que los unen.',
            'release_year' => 2022,
            'genres' => ['Drama', 'Misterio', 'Familiar'],
            'number_of_seasons' => 1,
            'episodes_per_season' => [8],
            'poster' => 'storage/posters/poster-10.jpg',
            'backdrop' => 'storage/backdrops/backdrop-5.jpg',
        ],
    ];

    /**
     * Sample Chinese drama data
     */
    protected $cDramas = [
        [
            'title' => 'La Emperatriz Guerrera',
            'original_title' => '战争女皇',
            'synopsis' => 'La historia de una joven que asciende desde la adversidad hasta convertirse en la emperatriz más poderosa de la historia china, enfrentando intrigas y traiciones.',
            'release_year' => 2023,
            'genres' => ['Histórico', 'Drama', 'Acción'],
            'number_of_seasons' => 1,
            'episodes_per_season' => [40],
            'poster' => 'storage/posters/poster-1.jpg',
            'backdrop' => 'storage/backdrops/backdrop-5.jpg',
        ],
        [
            'title' => 'Amor en la Ciudad Prohibida',
            'original_title' => '紫禁城之恋',
            'synopsis' => 'El romance prohibido entre un guardia imperial y la concubina del emperador en la antigua China, una historia de pasión, sacrificio y traición.',
            'release_year' => 2022,
            'genres' => ['Romance', 'Histórico', 'Drama'],
            'number_of_seasons' => 1,
            'episodes_per_season' => [36],
            'poster' => 'storage/posters/poster-2.jpg',
            'backdrop' => 'storage/backdrops/backdrop-4.jpg',
        ],
        [
            'title' => 'El Maestro de las Artes',
            'original_title' => '艺术大师',
            'synopsis' => 'Un prodigio de las artes marciales con un pasado misterioso se convierte en maestro de una escuela decadente, enfrentando rivales y descubriendo secretos ancestrales.',
            'release_year' => 2023,
            'genres' => ['Acción', 'Fantasía', 'Histórico'],
            'number_of_seasons' => 1,
            'episodes_per_season' => [45],
            'poster' => 'storage/posters/poster-3.jpg',
            'backdrop' => 'storage/backdrops/backdrop-3.jpg',
        ],
        [
            'title' => 'Destinos Entrelazados',
            'original_title' => '纠缠的命运',
            'synopsis' => 'Una historia contemporánea sobre tres parejas cuyos destinos se entrelazan a través de coincidencias y viejas conexiones, explorando el verdadero significado del amor.',
            'release_year' => 2022,
            'genres' => ['Romance', 'Drama', 'Vida cotidiana'],
            'number_of_seasons' => 1,
            'episodes_per_season' => [30],
            'poster' => 'storage/posters/poster-4.jpg',
            'backdrop' => 'storage/backdrops/backdrop-2.jpg',
        ],
        [
            'title' => 'La Leyenda del Río Azul',
            'original_title' => '蓝河传说',
            'synopsis' => 'Una fantasía épica sobre dioses, demonios y humanos luchando por el control de un artefacto místico que puede alterar el destino del mundo.',
            'release_year' => 2021,
            'genres' => ['Fantasía', 'Acción', 'Romance'],
            'number_of_seasons' => 1,
            'episodes_per_season' => [50],
            'poster' => 'storage/posters/poster-5.jpg',
            'backdrop' => 'storage/backdrops/backdrop-1.jpg',
        ],
    ];

    /**
     * Sample Asian movies data
     */
    protected $asianMovies = [
        [
            'title' => 'El último tren a Seúl',
            'original_title' => '서울행 마지막 기차',
            'synopsis' => 'Durante un apocalipsis zombie, un grupo de pasajeros lucha por sobrevivir en un tren de alta velocidad con destino a Seúl, el último lugar seguro.',
            'release_year' => 2023,
            'country' => 'Corea del Sur',
            'genres' => ['Acción', 'Horror', 'Thriller'],
            'duration' => 118,
            'poster' => 'storage/posters/poster-6.jpg',
            'backdrop' => 'storage/backdrops/backdrop-1.jpg',
        ],
        [
            'title' => 'Confesiones de Tokio',
            'original_title' => '東京告白',
            'synopsis' => 'Un profesor universitario descubre un diario que revela oscuros secretos sobre la muerte de su esposa, llevándolo a un viaje por el bajo mundo de Tokio.',
            'release_year' => 2022,
            'country' => 'Japón',
            'genres' => ['Thriller', 'Drama', 'Misterio'],
            'duration' => 124,
            'poster' => 'storage/posters/poster-7.jpg',
            'backdrop' => 'storage/backdrops/backdrop-2.jpg',
        ],
        [
            'title' => 'La Danza del Dragón',
            'original_title' => '龙之舞',
            'synopsis' => 'Un maestro de artes marciales retirado debe volver a la acción cuando su pueblo es amenazado por una corporación corrupta con conexiones al crimen organizado.',
            'release_year' => 2023,
            'country' => 'China',
            'genres' => ['Acción', 'Drama', 'Crimen'],
            'duration' => 131,
            'poster' => 'storage/posters/poster-8.jpg',
            'backdrop' => 'storage/backdrops/backdrop-3.jpg',
        ],
        [
            'title' => 'Flores en Invierno',
            'original_title' => '겨울에 피는 꽃',
            'synopsis' => 'La conmovedora historia de una paciente terminal que decide hacer una lista de deseos y el médico que la ayuda a cumplirlos, encontrando ambos una nueva perspectiva sobre la vida.',
            'release_year' => 2022,
            'country' => 'Corea del Sur',
            'genres' => ['Drama', 'Romance', 'Médico'],
            'duration' => 127,
            'poster' => 'storage/posters/poster-9.jpg',
            'backdrop' => 'storage/backdrops/backdrop-4.jpg',
        ],
        [
            'title' => 'El Susurro del Bosque',
            'original_title' => '森のささやき',
            'synopsis' => 'Una artista que se muda a un pueblo remoto en las montañas japonesas comienza a experimentar sucesos paranormales relacionados con una antigua leyenda local.',
            'release_year' => 2022,
            'country' => 'Japón',
            'genres' => ['Horror', 'Misterio', 'Sobrenatural'],
            'duration' => 109,
            'poster' => 'storage/posters/poster-10.jpg',
            'backdrop' => 'storage/backdrops/backdrop-5.jpg',
        ],
    ];

    /**
     * Sample actors and directors
     */
    protected $people = [
        [
            'name' => 'Kim Min-ji',
            'original_name' => '김민지',
            'country' => 'Corea del Sur',
            'biography' => 'Actriz coreana conocida por sus papeles en dramas románticos y de época. Comenzó su carrera como modelo antes de dar el salto a la actuación.',
            'photo' => 'storage/profiles/default.jpg',
        ],
        [
            'name' => 'Lee Ji-hoon',
            'original_name' => '이지훈',
            'country' => 'Corea del Sur',
            'biography' => 'Actor y cantante coreano, reconocido por su versatilidad en diferentes géneros, desde el romance hasta la acción.',
            'photo' => 'storage/profiles/default.jpg',
        ],
        [
            'name' => 'Park Soo-hyun',
            'original_name' => '박수현',
            'country' => 'Corea del Sur',
            'biography' => 'Director coreano premiado internacionalmente por su estilo visual único y narrativas profundas.',
            'photo' => 'storage/profiles/default.jpg',
        ],
        [
            'name' => 'Tanaka Yuki',
            'original_name' => '田中ゆき',
            'country' => 'Japón',
            'biography' => 'Actriz japonesa reconocida por sus papeles en dramas de NHK y películas independientes.',
            'photo' => 'storage/profiles/default.jpg',
        ],
        [
            'name' => 'Yamamoto Kenji',
            'original_name' => '山本健二',
            'country' => 'Japón',
            'biography' => 'Actor japonés con experiencia en teatro, televisión y cine, especializado en personajes complejos.',
            'photo' => 'storage/profiles/default.jpg',
        ],
        [
            'name' => 'Nakamura Hanako',
            'original_name' => '中村花子',
            'country' => 'Japón',
            'biography' => 'Directora japonesa reconocida por sus dramas sociales y psicológicos basados en la vida cotidiana.',
            'photo' => 'storage/profiles/default.jpg',
        ],
        [
            'name' => 'Chen Wei',
            'original_name' => '陈伟',
            'country' => 'China',
            'biography' => 'Actor chino especializado en dramas históricos y de artes marciales.',
            'photo' => 'storage/profiles/default.jpg',
        ],
        [
            'name' => 'Zhang Li Mei',
            'original_name' => '张丽梅',
            'country' => 'China',
            'biography' => 'Actriz china reconocida por su belleza y talento en dramas románticos e históricos.',
            'photo' => 'storage/profiles/default.jpg',
        ],
        [
            'name' => 'Wang Feng',
            'original_name' => '王峰',
            'country' => 'China',
            'biography' => 'Director chino, reconocido por sus épicas históricas y efectos visuales revolucionarios.',
            'photo' => 'storage/profiles/default.jpg',
        ],
    ];

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $count = min((int) $this->option('count'), 5); // Máximo 5 títulos por categoría (es lo que tenemos definido)

        $this->info("Generando datos de muestra para Dorasia (tipo: {$type}, cantidad: {$count})");

        // Asegurarse de que existen las categorías
        if (!Category::count()) {
            $this->call('db:seed', ['--class' => 'CategorySeeder']);
        }

        // Asegurarse de que existen los géneros
        if (!Genre::count()) {
            $this->call('db:seed', ['--class' => 'GenreSeeder']);
        }
        
        // Crear gente de muestra
        $this->createSamplePeople();

        // Generar títulos según el tipo seleccionado
        if ($type === 'all' || $type === 'k-drama') {
            $this->createSampleTitles('k-drama', array_slice($this->kDramas, 0, $count));
        }
        
        if ($type === 'all' || $type === 'j-drama') {
            $this->createSampleTitles('j-drama', array_slice($this->jDramas, 0, $count));
        }
        
        if ($type === 'all' || $type === 'c-drama') {
            $this->createSampleTitles('c-drama', array_slice($this->cDramas, 0, $count));
        }
        
        if ($type === 'all' || $type === 'movie') {
            $this->createSampleTitles('peliculas', array_slice($this->asianMovies, 0, $count));
        }

        $this->info('¡Datos de muestra generados correctamente!');
    }

    /**
     * Create sample people (actors, directors)
     */
    protected function createSamplePeople()
    {
        $this->info('Creando perfiles de personas de muestra...');
        
        $progressBar = $this->output->createProgressBar(count($this->people));
        $progressBar->start();
        
        foreach ($this->people as $personData) {
            Person::updateOrCreate(
                ['name' => $personData['name']],
                array_merge($personData, [
                    'slug' => Str::slug($personData['name']),
                ])
            );
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->line('');
    }

    /**
     * Create sample titles for a specific category
     */
    protected function createSampleTitles(string $categorySlug, array $titles)
    {
        $category = Category::where('slug', $categorySlug)->first();
        
        if (!$category) {
            $this->error("La categoría {$categorySlug} no existe");
            return;
        }
        
        $this->info("Creando títulos de muestra para la categoría: {$category->name}");
        
        $progressBar = $this->output->createProgressBar(count($titles));
        $progressBar->start();
        
        foreach ($titles as $titleData) {
            try {
                DB::beginTransaction();
                
                // Determinar si es serie o película
                $isMovie = $categorySlug === 'peliculas';
                
                // Preparar datos para el título
                $titleAttributes = [
                    'title' => $titleData['title'],
                    'original_title' => $titleData['original_title'],
                    'synopsis' => $titleData['synopsis'],
                    'type' => $isMovie ? 'movie' : 'series',
                    'country' => $titleData['country'] ?? ($categorySlug === 'k-drama' ? 'Corea del Sur' : ($categorySlug === 'j-drama' ? 'Japón' : 'China')),
                    'release_year' => $titleData['release_year'],
                    'category_id' => $category->id,
                    'slug' => Str::slug($titleData['title']),
                    'poster' => $titleData['poster'],
                    'backdrop' => $titleData['backdrop'],
                    'featured' => rand(0, 1) === 1,
                ];
                
                // Añadir campos específicos para película
                if ($isMovie) {
                    $titleAttributes['duration'] = $titleData['duration'];
                } else {
                    $titleAttributes['number_of_seasons'] = $titleData['number_of_seasons'];
                    $titleAttributes['number_of_episodes'] = array_sum($titleData['episodes_per_season']);
                }
                
                // Crear o actualizar el título
                $title = Title::updateOrCreate(
                    ['slug' => $titleAttributes['slug']],
                    $titleAttributes
                );
                
                // Asignar géneros
                $genreIds = [];
                foreach ($titleData['genres'] as $genreName) {
                    $genre = Genre::where('name', $genreName)->first();
                    if ($genre) {
                        $genreIds[] = $genre->id;
                    }
                }
                
                // Verificar que estamos usando el nombre correcto de la tabla pivot
                DB::table('title_genre')->where('title_id', $title->id)->delete();
                foreach ($genreIds as $genreId) {
                    DB::table('title_genre')->insert([
                        'title_id' => $title->id,
                        'genre_id' => $genreId
                    ]);
                }
                
                // Si es una serie, crear temporadas y episodios
                if (!$isMovie) {
                    $this->createSeasonsAndEpisodes($title, $titleData['number_of_seasons'], $titleData['episodes_per_season']);
                }
                
                // Asignar actores y directores
                $this->assignPeopleTotitle($title);
                
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Error creando título {$titleData['title']}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->line('');
    }

    /**
     * Create seasons and episodes for a title
     */
    protected function createSeasonsAndEpisodes(Title $title, int $seasonCount, array $episodesCounts)
    {
        for ($s = 1; $s <= $seasonCount; $s++) {
            $season = Season::updateOrCreate(
                [
                    'title_id' => $title->id,
                    'number' => $s,
                ],
                [
                    'title' => "Temporada {$s}",
                    'overview' => "Temporada {$s} de {$title->title}",
                    'episode_count' => $episodesCounts[$s-1],
                    'poster' => $title->poster,
                ]
            );
            
            $episodeCount = $episodesCounts[$s-1];
            
            for ($e = 1; $e <= $episodeCount; $e++) {
                Episode::updateOrCreate(
                    [
                        'season_id' => $season->id,
                        'number' => $e,
                    ],
                    [
                        'name' => "Episodio {$e}",  // Cambiado de 'title' a 'name'
                        'overview' => "Episodio {$e} de la temporada {$s} de {$title->title}", // Cambiado de 'synopsis' a 'overview'
                        'runtime' => rand(45, 70),  // Cambiado de 'duration' a 'runtime'
                    ]
                );
            }
        }
    }

    /**
     * Assign people (actors, directors) to a title
     */
    protected function assignPeopleTotitle(Title $title)
    {
        $actorData = [];
        
        // Determinar personas por país
        $country = $title->country;
        $people = Person::where('country', $country)->get();
        
        if ($people->isEmpty()) {
            $people = Person::all();
        }
        
        // Seleccionar actores
        $actors = $people->filter(function ($person) {
            return !Str::contains(strtolower($person->biography ?? ''), 'director');
        });
        
        // Seleccionar directores
        $directors = $people->filter(function ($person) {
            return Str::contains(strtolower($person->biography ?? ''), 'director');
        });
        
        // Asignar actores
        foreach ($actors->take(3) as $index => $actor) {
            $actorData[$actor->id] = [
                'role' => 'actor',
                'character' => 'Personaje ' . ($index + 1),
                'order' => $index,
            ];
        }
        
        // Asignar director
        if ($directors->isNotEmpty()) {
            $director = $directors->first();
            $actorData[$director->id] = [
                'role' => 'director',
                'character' => null,
                'order' => 0,
            ];
        }
        
        // Sincronizar relaciones usando directamente la tabla pivot
        DB::table('title_person')->where('title_id', $title->id)->delete();
        foreach ($actorData as $personId => $data) {
            DB::table('title_person')->insert([
                'title_id' => $title->id,
                'person_id' => $personId,
                'role' => $data['role'],
                'character' => $data['character'],
                'order' => $data['order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}