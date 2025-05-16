<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Models\Person;
use App\Models\Title;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CleanAndGenerateAsianNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dorasia:clean-and-generate-news 
                            {--clean : Clean non-Asian content news}
                            {--generate : Generate new Asian-focused news}
                            {--limit=10 : Number of news to generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean non-Asian content news and generate new Asian entertainment focused news';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('clean')) {
            $this->cleanNonAsianNews();
        }
        
        if ($this->option('generate')) {
            $this->generateAsianNews();
        }
        
        if (!$this->option('clean') && !$this->option('generate')) {
            $this->info('Please use --clean or --generate option');
        }
    }
    
    /**
     * Clean news that are not related to Asian entertainment
     */
    protected function cleanNonAsianNews()
    {
        $this->info('Cleaning non-Asian content news...');
        
        // Keywords that indicate non-Asian content
        $nonAsianKeywords = [
            'basketball', 'football', 'baseball', 'hockey', 'nfl', 'nba', 'mlb', 'nhl',
            'university', 'college', 'american sports', 'us news', 'politics',
            'indiana', 'michigan', 'california', 'texas', 'florida'
        ];
        
        // Keywords that indicate Asian content
        $asianKeywords = [
            'drama', 'dorama', 'kdrama', 'cdrama', 'jdrama', 'thai drama',
            'korea', 'korean', 'japan', 'japanese', 'china', 'chinese', 'taiwan', 'thai',
            'actor', 'actriz', 'película', 'serie', 'episodio',
            'seoul', 'tokyo', 'beijing', 'shanghai', 'bangkok',
            'hallyu', 'kpop', 'k-pop', 'anime', 'manga'
        ];
        
        $newsToDelete = News::where(function($query) use ($nonAsianKeywords) {
            foreach ($nonAsianKeywords as $keyword) {
                $query->orWhere('title', 'LIKE', "%{$keyword}%")
                      ->orWhere('content', 'LIKE', "%{$keyword}%");
            }
        })->where(function($query) use ($asianKeywords) {
            foreach ($asianKeywords as $keyword) {
                $query->where('title', 'NOT LIKE', "%{$keyword}%")
                      ->where('content', 'NOT LIKE', "%{$keyword}%");
            }
        })->get();
        
        $count = $newsToDelete->count();
        
        foreach ($newsToDelete as $news) {
            $this->info("Deleting: {$news->title}");
            $news->delete();
        }
        
        $this->info("Deleted {$count} non-Asian content news articles.");
    }
    
    /**
     * Generate new Asian entertainment focused news
     */
    protected function generateAsianNews()
    {
        $limit = $this->option('limit');
        $this->info("Generating {$limit} Asian entertainment news articles...");
        
        // Sample news templates for Asian entertainment
        $newsTemplates = [
            [
                'title' => 'Kim Soo-hyun y IU en conversaciones para protagonizar nuevo drama romántico',
                'content' => 'Los aclamados actores Kim Soo-hyun y IU están en conversaciones finales para protagonizar el esperado drama romántico "Luna de Medianoche". La serie contará la historia de un pianista talentoso que perdió su pasión por la música hasta que conoce a una cantante misteriosa. Si se confirma, será la primera colaboración entre estos dos grandes artistas del entretenimiento coreano.',
                'source_name' => 'Noticias K-Drama',
                'actor_image' => '/posters/kim-soo-hyun.jpg'
            ],
            [
                'title' => 'Song Hye-kyo revela detalles sobre su nuevo proyecto con Netflix',
                'content' => 'La estrella Song Hye-kyo compartió emocionantes detalles sobre su próxima serie original de Netflix "La Gloria de la Venganza". Durante una entrevista exclusiva, la actriz mencionó que el papel la desafió física y emocionalmente, requiriendo meses de preparación. La serie, que explora temas de venganza y redención, se estrenará mundialmente en marzo de 2025.',
                'source_name' => 'Entretenimiento Asia',
                'actor_image' => '/posters/song-hye-kyo.jpg'
            ],
            [
                'title' => 'Park Seo-joon se une al elenco de película de Marvel filmada en Seúl',
                'content' => 'El popular actor Park Seo-joon ha sido confirmado para un papel importante en la próxima película de Marvel que se filmará parcialmente en Seúl. Aunque los detalles del personaje se mantienen en secreto, fuentes cercanas indican que será un superhéroe coreano que jugará un papel crucial en la Fase 5 del MCU. Las filmaciones comenzarán en junio de 2025.',
                'source_name' => 'Cine Asiático Hoy',
                'actor_image' => '/posters/park-seo-joon.jpg'
            ],
            [
                'title' => 'Ji Chang-wook anuncia gira de fans meetings por Latinoamérica',
                'content' => 'El carismático actor Ji Chang-wook ha anunciado oficialmente su primera gira de fans meetings por Latinoamérica. La gira incluirá paradas en Ciudad de México, São Paulo, Buenos Aires y Santiago de Chile durante el verano de 2025. Los fans latinoamericanos han expresado su entusiasmo por finalmente poder conocer en persona al actor de "Empress Ki" y "Healer".',
                'source_name' => 'Hallyu Latino',
                'actor_image' => '/posters/ji-chang-wook.jpg'
            ],
            [
                'title' => 'Jun Ji-hyun regresa a la televisión después de 5 años con thriller psicológico',
                'content' => 'La icónica actriz Jun Ji-hyun hará su esperado regreso a los doramas con el thriller psicológico "Silencio Mortal". Después de 5 años enfocada en películas, la estrella de "Mi Amor de las Estrellas" interpretará a una detective que investiga una serie de misteriosos asesinatos. La producción promete ser uno de los dramas más intensos del año.',
                'source_name' => 'Drama Total',
                'actor_image' => '/posters/jun-ji-hyun.jpg'
            ],
            [
                'title' => 'Lee Min-ho confirma romance histórico con guionista de "Goblin"',
                'content' => 'Lee Min-ho protagonizará el nuevo drama histórico escrito por la aclamada guionista de "Goblin", Kim Eun-sook. La serie, titulada "El Príncipe del Alba", mezclará elementos fantásticos con la historia de Corea. Lee Min-ho interpretará a un príncipe inmortal que ha vivido a través de diferentes épocas históricas. El drama se estrenará en la segunda mitad de 2025.',
                'source_name' => 'K-Drama Exclusivo',
                'actor_image' => '/posters/lee-min-ho.jpg'
            ],
            [
                'title' => 'Bae Suzy gana premio a Mejor Actriz en los Baeksang Arts Awards',
                'content' => 'Bae Suzy se llevó el prestigioso premio a Mejor Actriz en los Baeksang Arts Awards por su conmovedora actuación en el drama "Mientras Dormías". En su discurso de aceptación, la actriz agradeció a sus fans y prometió seguir desafiándose con papeles diversos. Este es el segundo Baeksang de Suzy, consolidándola como una de las actrices más versátiles de su generación.',
                'source_name' => 'Premios y Celebridades',
                'actor_image' => '/posters/bae-suzy.jpg'
            ],
            [
                'title' => 'Hyun Bin y Son Ye-jin esperan su segundo bebé',
                'content' => 'La pareja dorada del entretenimiento coreano, Hyun Bin y Son Ye-jin, anunciaron que esperan su segundo hijo. La noticia llega dos años después del nacimiento de su primer bebé. Ambos actores expresaron su felicidad y agradecieron el apoyo continuo de sus fans. La pareja planea tomarse un descanso de sus actividades profesionales para enfocarse en su familia.',
                'source_name' => 'Estrellas de Corea',
                'actor_image' => '/posters/hyun-bin-son-ye-jin.jpg'
            ],
            [
                'title' => 'Park Bo-gum inicia servicio militar alternativo como trabajador social',
                'content' => 'El querido actor Park Bo-gum comenzó su servicio militar alternativo como trabajador social cultural. A diferencia del servicio militar tradicional, Park Bo-gum cumplirá su deber nacional promoviendo la cultura coreana en el extranjero. Sus fans han expresado apoyo y esperan pacientemente su regreso completo a la actuación en 2025.',
                'source_name' => 'Noticias del Servicio',
                'actor_image' => '/posters/park-bo-gum.jpg'
            ],
            [
                'title' => 'Kim Ji-won y Kim Soo-hyun nominados a mejor química en pantalla',
                'content' => 'La pareja protagonista de "Queen of Tears", Kim Ji-won y Kim Soo-hyun, han sido nominados al premio de Mejor Química en Pantalla en los Asia Drama Awards. Su emotiva interpretación de una pareja atravesando crisis matrimoniales cautivó a audiencias en toda Asia. Los fans han iniciado campañas de votación masivas para apoyar a sus actores favoritos.',
                'source_name' => 'Premios Drama Asia',
                'actor_image' => '/posters/kim-ji-won.jpg'
            ],
            [
                'title' => 'Jung Hae-in revela transformación física para nuevo rol de acción',
                'content' => 'Jung Hae-in sorprendió a sus fans al revelar una impresionante transformación física para su próximo papel en la película de acción "Operación Tormenta". El actor ganó 10 kilos de músculo y entrenó en artes marciales durante 6 meses. La película, que se estrenará en cines en mayo de 2025, marca el debut de Jung Hae-in en el género de acción pura.',
                'source_name' => 'Acción Cinema Asia',
                'actor_image' => '/posters/jung-hae-in.jpg'
            ],
            [
                'title' => 'Suzy y Park Bo-gum confirmados para drama romántico de época',
                'content' => 'Suzy y Park Bo-gum han sido confirmados como protagonistas del drama de época "Amor Bajo las Estrellas de Joseon". La serie, ambientada en el siglo XVIII, contará la historia de amor prohibido entre una noble y un erudito pobre. Esta será la primera vez que ambos actores trabajen juntos, generando gran expectativa entre los fans de dramas históricos.',
                'source_name' => 'Dramas Históricos',
                'actor_image' => '/posters/suzy-park-bo-gum.jpg'
            ],
            [
                'title' => 'Song Joong-ki anuncia nuevo proyecto con los hermanos Duffer de "Stranger Things"',
                'content' => 'Song Joong-ki se une a la nueva serie de ciencia ficción de los hermanos Duffer, creadores de "Stranger Things". El actor coreano tendrá un papel principal en esta producción internacional que mezclará elementos de horror cósmico con mitología asiática. Las filmaciones comenzarán en Los Ángeles y Seúl a principios de 2025.',
                'source_name' => 'Producciones Globales',
                'actor_image' => '/posters/song-joong-ki.jpg'
            ],
            [
                'title' => 'IU lanza nuevo álbum y anuncia conciertos en estadios por Asia',
                'content' => 'La multi-talentosa IU ha lanzado su esperado nuevo álbum "Lilac Dreams" y anunció una gira masiva por estadios de Asia. La gira incluirá presentaciones en Tokio, Bangkok, Manila, Singapur y Hong Kong. IU promete un espectáculo completamente nuevo con colaboraciones especiales de otros artistas del K-pop.',
                'source_name' => 'Música K-Pop Hoy',
                'actor_image' => '/posters/iu.jpg'
            ],
            [
                'title' => 'Gong Yoo y Seo Hyun-jin protagonizarán thriller noir para HBO Asia',
                'content' => 'Los aclamados actores Gong Yoo y Seo Hyun-jin han sido elegidos para protagonizar el thriller noir "Ciudad Sin Nombre" de HBO Asia. La serie de 8 episodios explorará el submundo criminal de Seúl a través de los ojos de un detective corrupto y una periodista decidida a exponer la verdad. La producción promete ser una de las más oscuras y complejas del año.',
                'source_name' => 'Series Premium Asia',
                'actor_image' => '/posters/gong-yoo.jpg'
            ],
            // Nuevas noticias agregadas
            [
                'title' => 'BTS anuncia colaboración especial con el drama "Youth Forever"',
                'content' => 'El grupo de K-pop BTS participará en la banda sonora del esperado drama juvenil "Youth Forever". Además, los miembros harán apariciones especiales como mentores de los protagonistas. La serie explorará los sueños y desafíos de jóvenes artistas en la industria del entretenimiento coreano. Los fans de BTS están emocionados por ver esta nueva faceta del grupo.',
                'source_name' => 'K-Pop y Drama News',
                'actor_image' => '/posters/bts.jpg'
            ],
            [
                'title' => 'Park Shin-hye regresa con drama de suspenso médico tras maternidad',
                'content' => 'Park Shin-hye hace su esperado regreso a la actuación con el thriller médico "Doctor Frost", donde interpretará a una brillante psiquiatra que resuelve casos misteriosos. Este es su primer proyecto tras convertirse en madre. La actriz compartió que el papel le resultó desafiante pero gratificante, y que está emocionada de reconectarse con sus fans.',
                'source_name' => 'Retornos Estelares',
                'actor_image' => '/posters/park-shin-hye.jpg'
            ],
            [
                'title' => 'Lee Jong-suk y Han Hyo-joo unidos en romance sobrenatural',
                'content' => 'Los talentosos actores Lee Jong-suk y Han Hyo-joo protagonizarán el drama romántico con elementos sobrenaturales "Between Worlds". La historia sigue a un escritor que puede viajar entre dimensiones paralelas y una doctora que puede ver fantasmas. La química entre los protagonistas promete cautivar a las audiencias cuando se estrene en otoño de 2025.',
                'source_name' => 'Romance y Fantasía',
                'actor_image' => '/posters/lee-jong-suk.jpg'
            ],
            [
                'title' => 'Netflix ordena segunda temporada de "Squid Game" con cast original',
                'content' => 'Netflix confirmó oficialmente la segunda temporada de "Squid Game" con el regreso del elenco original incluyendo a Lee Jung-jae y Park Hae-soo. El creador Hwang Dong-hyuk reveló que la nueva temporada explorará las consecuencias de los eventos de la primera y presentará juegos aún más intensos. La producción comenzará a mediados de 2025.',
                'source_name' => 'Streaming Internacional',
                'actor_image' => '/posters/squid-game.jpg'
            ],
            [
                'title' => 'Cha Eun-woo debuta como protagonista en drama de acción',
                'content' => 'El idol y actor Cha Eun-woo asume su primer papel principal de acción en el drama "Black Knight". Interpretará a un agente secreto que debe proteger a una heredera mientras descubre una conspiración internacional. Cha Eun-woo entrenó intensamente durante meses para las escenas de acción y promete mostrar una faceta completamente nueva.',
                'source_name' => 'Idols en Actuación',
                'actor_image' => '/posters/cha-eun-woo.jpg'
            ],
            [
                'title' => 'Kim Tae-ri y Nam Joo-hyuk confirmados para drama histórico épico',
                'content' => 'Las estrellas en ascenso Kim Tae-ri y Nam Joo-hyuk han sido confirmadas para protagonizar el drama histórico de gran presupuesto "Imperio de las Sombras". Ambientado en la era Goryeo, la serie narrará la historia de una guerrera y un príncipe que luchan contra invasores extranjeros. Con un presupuesto récord, promete ser una producción visualmente espectacular.',
                'source_name' => 'Épicas Históricas',
                'actor_image' => '/posters/kim-tae-ri.jpg'
            ],
            [
                'title' => 'Yoo Jae-suk hace debut como actor en comedia familiar',
                'content' => 'El legendario presentador Yoo Jae-suk sorprende al hacer su debut como actor en la comedia familiar "Papa Running Man". Interpretará a un padre soltero que debe cuidar a sus tres hijos mientras mantiene su carrera como entrenador deportivo. Los fans están curiosos por ver las habilidades actorales del "MC Nacional" de Corea.',
                'source_name' => 'Debuts Sorprendentes',
                'actor_image' => '/posters/yoo-jae-suk.jpg'
            ],
            [
                'title' => 'BLACKPINK protagonizará su propio drama musical en 2025',
                'content' => 'El grupo femenino BLACKPINK protagonizará un drama musical especial titulado "Pink Revolution". Cada miembro interpretará una versión ficticia de sí misma en una historia sobre amistad, fama y los desafíos de ser ídolos globales. El proyecto incluirá nuevas canciones exclusivas y promete mostrar las habilidades actorales de Jisoo, Jennie, Rosé y Lisa.',
                'source_name' => 'K-Pop Drama Fusion',
                'actor_image' => '/posters/blackpink.jpg'
            ],
            [
                'title' => 'Jung Ho-yeon de "Squid Game" protagonizará thriller psicológico internacional',
                'content' => 'Jung Ho-yeon, quien saltó a la fama mundial con "Squid Game", ha sido elegida para protagonizar el thriller psicológico "The Glass House" junto a Oscar Isaac. La producción será una colaboración entre Corea y Hollywood, filmándose en Seúl y Nueva York. Jung Ho-yeon interpretará a una arquitecta que descubre oscuros secretos en su nuevo proyecto.',
                'source_name' => 'Producciones Globales',
                'actor_image' => '/posters/jung-ho-yeon.jpg'
            ],
            [
                'title' => 'Remake coreano de "La Casa de Papel" revela elenco estelar',
                'content' => 'El esperado remake coreano de "La Casa de Papel" ha revelado su impresionante elenco incluyendo a Yoo Ji-tae como el Profesor, Park Hae-soo como Berlín y Jun Jong-seo como Tokio. La adaptación trasladará la historia a la Casa de la Moneda de Corea y explorará temas de reunificación. Netflix planea un estreno mundial simultáneo.',
                'source_name' => 'Remakes Internacionales',
                'actor_image' => '/posters/money-heist-korea.jpg'
            ]
        ];
        
        // Get random actors to associate with news
        $actors = Person::inRandomOrder()->take(count($newsTemplates))->get();
        
        foreach ($newsTemplates as $index => $template) {
            if ($index >= $limit) break;
            
            // Verificar si ya existe una noticia con este título
            if (News::where('slug', Str::slug($template['title']))->exists()) {
                $this->warn("Noticia ya existe: {$template['title']}");
                continue;
            }
            
            // Usar la imagen del actor si está disponible, sino usar placeholder
            $imagePath = $template['actor_image'] ?? "images/news/news-placeholder-" . rand(1, 5) . ".jpg";
            
            $news = News::create([
                'title' => $template['title'],
                'slug' => Str::slug($template['title']),
                'content' => $template['content'],
                'source_name' => $template['source_name'],
                'image' => $imagePath,
                'published_at' => Carbon::now()->subDays(rand(1, 7)),
                'featured' => rand(0, 1) == 1,
            ]);
            
            // Buscar actores mencionados por nombre en el título o contenido
            $actorNames = ['Kim Soo-hyun', 'IU', 'Song Hye-kyo', 'Park Seo-joon', 'Ji Chang-wook', 
                          'Jun Ji-hyun', 'Lee Min-ho', 'Bae Suzy', 'Hyun Bin', 'Son Ye-jin',
                          'Park Bo-gum', 'Kim Ji-won', 'Jung Hae-in', 'Song Joong-ki', 'Gong Yoo',
                          'Seo Hyun-jin', 'Park Shin-hye', 'Lee Jong-suk', 'Han Hyo-joo',
                          'Lee Jung-jae', 'Park Hae-soo', 'Cha Eun-woo', 'Kim Tae-ri',
                          'Nam Joo-hyuk', 'Yoo Jae-suk', 'Jung Ho-yeon'];
            
            $attachedActors = [];
            foreach ($actorNames as $actorName) {
                if (str_contains($template['title'] . ' ' . $template['content'], $actorName)) {
                    $actor = Person::where('name', 'LIKE', '%' . $actorName . '%')->first();
                    if ($actor && !in_array($actor->id, $attachedActors)) {
                        $news->people()->attach($actor->id, [
                            'primary_subject' => count($attachedActors) == 0
                        ]);
                        $attachedActors[] = $actor->id;
                    }
                }
            }
            
            if (count($attachedActors) > 0) {
                $this->info("Creada noticia: {$news->title} (Asociada con " . count($attachedActors) . " actores)");
            } else {
                $this->info("Creada noticia: {$news->title}");
            }
        }
        
        $this->info("Successfully generated {$limit} Asian entertainment news articles.");
        
        // Ejecutar validación de imágenes de actores
        if ($limit > 0) {
            $this->newLine();
            $this->info("Validando imágenes de actores en noticias...");
            $this->call('news:validate-actor-images', ['--fix' => true]);
        }
    }
}