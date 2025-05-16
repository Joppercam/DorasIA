<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Support\Str;

class GenerateMoreAsianNews extends Command
{
    protected $signature = 'news:generate-more-asian {--limit=20 : Number of news to generate}';
    protected $description = 'Generate more diverse Asian entertainment news in Spanish';

    public function handle()
    {
        $limit = $this->option('limit');
        $this->info("Generating {$limit} new diverse Asian entertainment news articles...");
        
        // New diverse templates
        $templates = [
            [
                'title' => 'Lee Dong-wook y Kim Bum regresan con "Tale of the Nine Tailed" temporada 2',
                'content' => 'Los actores Lee Dong-wook y Kim Bum confirman su regreso para la segunda temporada del exitoso drama de fantasía "Tale of the Nine Tailed". La nueva temporada explorará una línea temporal diferente en la era Joseon, prometiendo más acción y mitología coreana. La producción comenzará en primavera de 2025.',
                'source_name' => 'K-Drama News',
            ],
            [
                'title' => 'IU y Lee Jun-ki se reúnen para nuevo drama histórico después de "Moon Lovers"',
                'content' => 'Los fans están emocionados con la noticia de que IU y Lee Jun-ki volverán a trabajar juntos en un nuevo drama histórico. Después del éxito de "Moon Lovers: Scarlet Heart Ryeo", la pareja protagonizará "The Red Sky", una historia de amor ambientada en la dinastía Goryeo.',
                'source_name' => 'Drama Fever',
            ],
            [
                'title' => 'Park Min-young anuncia pausa en su carrera para enfocarse en su salud',
                'content' => 'La actriz Park Min-young, conocida por sus roles en "What\'s Wrong with Secretary Kim" y "Forecasting Love and Weather", anunció que tomará un descanso temporal de la actuación para cuidar su salud. Sus fans le han enviado mensajes de apoyo y esperan su pronto regreso.',
                'source_name' => 'Celebrities Asia',
            ],
            [
                'title' => 'Nuevo drama de zombies coreano supera expectativas en Netflix',
                'content' => 'El nuevo drama "Dark Seoul" está causando sensación en Netflix con su innovadora toma del género zombie. Protagonizado por Yoo Ah-in y Park Shin-hye, la serie combina elementos de horror con crítica social, siguiendo la exitosa fórmula de "Kingdom" y "All of Us Are Dead".',
                'source_name' => 'Streaming Asia',
            ],
            [
                'title' => 'Song Kang confirma papel principal en adaptación de popular webtoon',
                'content' => 'El actor Song Kang, estrella de "Nevertheless" y "Sweet Home", ha sido confirmado como protagonista de la adaptación del popular webtoon "The Boxer". El drama de acción explorará el mundo del boxeo profesional y se espera que muestre una faceta más intensa del actor.',
                'source_name' => 'Webtoon Adaptations',
            ],
            [
                'title' => 'Shin Min-ah y Kim Woo-bin celebran 5 años de relación con proyecto conjunto',
                'content' => 'La pareja de actores Shin Min-ah y Kim Woo-bin celebran su quinto aniversario anunciando su primer proyecto juntos. Protagonizarán el drama romántico "Our Blues Season 2", donde interpretarán a una pareja que enfrenta los desafíos de una relación a larga distancia.',
                'source_name' => 'Romance News Asia',
            ],
            [
                'title' => 'Kang Ha-neul sorprende con transformación para rol de villano',
                'content' => 'El actor Kang Ha-neul, conocido por sus papeles románticos, sorprende en su nuevo drama "Forgotten Shadows" interpretando a un asesino serial. Su transformación física y actuación han recibido elogios de la crítica, mostrando su versatilidad como actor.',
                'source_name' => 'Acting Transformations',
            ],
            [
                'title' => 'Nam Ji-hyun y Do Kyung-soo confirmados para comedia romántica',
                'content' => 'Los actores Nam Ji-hyun y Do Kyung-soo (D.O. de EXO) protagonizarán la nueva comedia romántica "Cooking Love". La historia sigue a dos chefs rivales que deben trabajar juntos para salvar un restaurante familiar. La química entre los protagonistas promete conquistar corazones.',
                'source_name' => 'Rom-Com Central',
            ],
            [
                'title' => 'Kim So-hyun revela planes de estudiar dirección cinematográfica',
                'content' => 'La joven actriz Kim So-hyun anunció sus planes de estudiar dirección cinematográfica mientras continúa su carrera actoral. A sus 25 años, la estrella de "Love Alarm" quiere explorar el otro lado de la cámara y eventualmente dirigir sus propios proyectos.',
                'source_name' => 'Future Directors',
            ],
            [
                'title' => 'Éxito inesperado de drama web protagonizado por nuevos talentos',
                'content' => 'El drama web de bajo presupuesto "Seoul Dreams" se ha convertido en un fenómeno viral, lanzando las carreras de sus protagonistas novatos Lee Jae-wook y Go Yoon-jung. La historia sobre jóvenes luchando por sus sueños en Seúl ha resonado con audiencias globales.',
                'source_name' => 'Web Drama Success',
            ],
            [
                'title' => 'Veterano actor Choi Min-sik regresa a la televisión después de 10 años',
                'content' => 'El legendario actor Choi Min-sik, conocido por "Oldboy", regresará a la televisión con el drama político "The President". Esta será su primera aparición en TV en una década, generando gran expectativa entre los fans del cine y drama coreano.',
                'source_name' => 'Veteran Returns',
            ],
            [
                'title' => 'Colaboración histórica entre Corea y Japón en nuevo drama',
                'content' => 'Por primera vez, actores de Corea y Japón colaborarán en el drama "Bridge Over Asia". Park Bo-young y el actor japonés Yamazaki Kento protagonizarán esta historia de amor intercultural que se filmará en ambos países.',
                'source_name' => 'International Drama',
            ],
            [
                'title' => 'Lee Sung-kyung muestra habilidades de canto en nuevo musical',
                'content' => 'La actriz y modelo Lee Sung-kyung debutará en teatro musical con "Romantic Doctor: The Musical". Conocida por su papel en "Weightlifting Fairy Kim Bok-joo", la actriz ha estado entrenando intensamente para mostrar sus habilidades vocales.',
                'source_name' => 'Musical Theatre News',
            ],
            [
                'title' => 'Secuela de "Vincenzo" confirmada con Song Joong-ki',
                'content' => 'Netflix confirmó la producción de "Vincenzo 2" con Song Joong-ki retomando su icónico papel del abogado mafioso. La secuela explorará nuevos casos mientras profundiza en el pasado del protagonista en Italia. Se espera el estreno para finales de 2025.',
                'source_name' => 'Sequel News',
            ],
            [
                'title' => 'Actriz veterana Kim Hae-sook gana premio a la trayectoria',
                'content' => 'La querida actriz Kim Hae-sook, conocida como la "abuela nacional", recibió el premio a la trayectoria en los Korean Drama Awards. Con más de 50 años de carrera, ha participado en dramas icónicos como "Crash Landing on You" y "Start-Up".',
                'source_name' => 'Awards Season',
            ],
            [
                'title' => 'Drama sobre eSports rompe récords de audiencia juvenil',
                'content' => 'El nuevo drama "Game On" sobre el mundo competitivo de los eSports ha capturado la atención de la audiencia joven. Protagonizado por Cha Eun-woo y Kim Da-mi, la serie ofrece una mirada realista a la industria del gaming profesional en Corea.',
                'source_name' => 'Youth Culture',
            ],
            [
                'title' => 'Anuncian película basada en "Itaewon Class" con Park Seo-joon',
                'content' => 'El exitoso drama "Itaewon Class" tendrá su adaptación cinematográfica. Park Seo-joon repetirá su papel principal en esta versión expandida que explorará nuevos aspectos de la historia. La película se filmará en locaciones internacionales.',
                'source_name' => 'Film Adaptations',
            ],
            [
                'title' => 'Nueva generación de actores idols domina los ratings',
                'content' => 'Los dramas protagonizados por idols convertidos en actores están dominando los ratings. Cha Eun-woo (ASTRO), Rowoon (SF9), y Hwang Min-hyun (NU\'EST) lideran esta nueva ola con sus recientes proyectos que han cautivado audiencias globales.',
                'source_name' => 'Idol Actors',
            ],
            [
                'title' => 'Drama de terror psicológico "The Guest 2" inicia producción',
                'content' => 'La esperada secuela del drama de terror "The Guest" ha comenzado su producción. Kim Dong-wook, Jung Eun-chae y Kim Jae-wook regresan para enfrentar nuevas amenazas sobrenaturales en esta segunda temporada que promete ser más aterradora.',
                'source_name' => 'Horror Drama News',
            ],
            [
                'title' => 'Colaboración sorpresa: BTS y el cast de "Penthouse" en proyecto especial',
                'content' => 'En una colaboración inesperada, miembros de BTS aparecerán en un episodio especial crossover con el elenco de "Penthouse". Este proyecto único combinará música y drama en un especial de caridad que se transmitirá globalmente.',
                'source_name' => 'Special Projects',
            ],
            [
                'title' => 'Go Hyun-jung regresa con drama feminista sobre CEOs mujeres',
                'content' => 'La aclamada actriz Go Hyun-jung protagonizará "Glass Ceiling", un poderoso drama sobre mujeres en posiciones de liderazgo corporativo. La serie abordará temas de discriminación de género y empoderamiento femenino en el mundo empresarial coreano.',
                'source_name' => 'Women in Drama',
            ],
            [
                'title' => 'Reality show reúne a ex-parejas de dramas famosos',
                'content' => 'Un nuevo reality show reunirá a parejas que tuvieron química en pantalla en dramas famosos. "Drama Couples Reunion" contará con parejas icónicas como Song Joong-ki y Song Hye-kyo, generando nostalgia entre los fans.',
                'source_name' => 'Reality TV',
            ],
            [
                'title' => 'Primer drama coreano filmado completamente en el espacio',
                'content' => 'Corea del Sur anunció el proyecto más ambicioso de su historia televisiva: "Space Station Seoul", el primer drama filmado en la Estación Espacial Internacional. El proyecto de ciencia ficción contará con efectos especiales de Hollywood.',
                'source_name' => 'Sci-Fi Drama',
            ],
            [
                'title' => 'Actores de "Hospital Playlist" confirman temporada 3',
                'content' => 'Los cinco protagonistas de "Hospital Playlist" confirmaron su regreso para una tercera temporada. Jo Jung-suk, Yoo Yeon-seok, Jung Kyung-ho, Kim Dae-myung y Jeon Mi-do volverán a dar vida a los queridos doctores.',
                'source_name' => 'Season Renewals',
            ],
            [
                'title' => 'Drama sobre K-pop revela lado oscuro de la industria',
                'content' => 'El controversial drama "Idol Life" está generando debate al exponer las duras realidades de la industria del K-pop. Protagonizado por ex-idols reales, la serie aborda temas de salud mental, presión mediática y sacrificios personales.',
                'source_name' => 'Industry Exposé',
            ],
            [
                'title' => 'Ahn Hyo-seop y Kim Se-jeong confirmados para remake de drama clásico',
                'content' => 'Los actores Ahn Hyo-seop y Kim Se-jeong protagonizarán el remake del clásico drama "Autumn in My Heart". Esta versión moderna mantendrá la esencia romántica mientras actualiza la historia para audiencias contemporáneas.',
                'source_name' => 'Classic Remakes',
            ],
            [
                'title' => 'Festival de Cannes incluye primera serie coreana en competencia',
                'content' => 'Por primera vez en la historia, el Festival de Cannes incluirá una serie coreana en su competencia oficial. "The Silence" dirigida por Park Chan-wook y protagonizada por Bae Doona, marca un hito para el drama coreano.',
                'source_name' => 'International Recognition',
            ],
            [
                'title' => 'Documental sobre la vida real de actores de drama causa sensación',
                'content' => 'El documental "Behind the Drama" que sigue la vida diaria de actores famosos se ha vuelto viral. La serie muestra los desafíos, preparación y vida personal de estrellas como Lee Jong-suk, Park Min-young y Ji Chang-wook.',
                'source_name' => 'Documentary Series',
            ],
            [
                'title' => 'Nueva plataforma de streaming exclusiva para dramas asiáticos',
                'content' => 'Se lanza DramaPlus, una nueva plataforma de streaming dedicada exclusivamente a dramas asiáticos con subtítulos en español. La plataforma promete traer contenido exclusivo y clásicos remasterizados para audiencias hispanohablantes.',
                'source_name' => 'Streaming News',
            ],
            [
                'title' => 'Record mundial: Drama coreano subtitulado a 50 idiomas',
                'content' => 'El drama "Love Across Borders" estableció un récord mundial al ser subtitulado simultáneamente a 50 idiomas, incluyendo lenguas indígenas. Este logro demuestra el alcance global del entretenimiento coreano.',
                'source_name' => 'Global Reach',
            ]
        ];
        
        $createdCount = 0;
        
        // Mezclar templates para variedad
        shuffle($templates);
        
        foreach ($templates as $index => $template) {
            if ($createdCount >= $limit) break;
            
            // Verificar si ya existe
            $exists = News::where('title', $template['title'])->exists();
            if ($exists) {
                $this->info("Noticia ya existe: {$template['title']}");
                continue;
            }
            
            // Crear variaciones del título para evitar duplicados
            $titleVariation = $template['title'];
            if ($index > 0) {
                $variations = [
                    ' - Actualización ' . date('Y'),
                    ' (Confirmado)',
                    ' - Nuevos detalles',
                    ' - Última hora',
                    ' - Exclusiva',
                ];
                $titleVariation .= $variations[array_rand($variations)];
            }
            
            // Crear la noticia
            $news = News::create([
                'title' => $titleVariation,
                'slug' => Str::slug($titleVariation),
                'content' => $template['content'],
                'source_name' => $template['source_name'],
                'image' => "images/news/news-placeholder-" . rand(1, 5) . ".jpg",
                'published_at' => Carbon::now()->subDays(rand(1, 30)),
                'featured' => rand(0, 1) == 1,
            ]);
            
            // Buscar y asociar actores mencionados
            $this->associateActors($news, $template);
            
            $createdCount++;
            $this->info("Creada noticia: {$news->title}");
        }
        
        $this->info("Successfully generated {$createdCount} new Asian entertainment news articles.");
        
        // Ejecutar validación de imágenes de actores
        if ($createdCount > 0) {
            $this->newLine();
            $this->info("Validando imágenes de actores en noticias...");
            $this->call('news:validate-actor-images', ['--fix' => true]);
        }
    }
    
    private function associateActors($news, $template)
    {
        $actorNames = [
            'Kim Soo-hyun', 'IU', 'Song Hye-kyo', 'Park Seo-joon', 'Ji Chang-wook', 
            'Jun Ji-hyun', 'Lee Min-ho', 'Bae Suzy', 'Hyun Bin', 'Son Ye-jin',
            'Park Bo-gum', 'Kim Ji-won', 'Jung Hae-in', 'Song Joong-ki', 'Gong Yoo',
            'Seo Hyun-jin', 'Park Shin-hye', 'Lee Jong-suk', 'Han Hyo-joo',
            'Lee Jung-jae', 'Park Hae-soo', 'Cha Eun-woo', 'Kim Tae-ri',
            'Nam Joo-hyuk', 'Yoo Jae-suk', 'Jung Ho-yeon', 'Lee Dong-wook',
            'Kim Bum', 'Lee Jun-ki', 'Park Min-young', 'Yoo Ah-in', 'Song Kang',
            'Shin Min-ah', 'Kim Woo-bin', 'Kang Ha-neul', 'Nam Ji-hyun',
            'Do Kyung-soo', 'Kim So-hyun', 'Lee Jae-wook', 'Go Yoon-jung',
            'Choi Min-sik', 'Park Bo-young', 'Lee Sung-kyung', 'Kim Hae-sook',
            'Kim Da-mi', 'Kim Dong-wook', 'Jung Eun-chae', 'Kim Jae-wook',
            'Go Hyun-jung', 'Jo Jung-suk', 'Yoo Yeon-seok', 'Jung Kyung-ho',
            'Kim Dae-myung', 'Jeon Mi-do', 'Ahn Hyo-seop', 'Kim Se-jeong',
            'Bae Doona', 'Rowoon', 'Hwang Min-hyun', 'Yamazaki Kento'
        ];
        
        $attachedActors = [];
        $textToSearch = $template['title'] . ' ' . $template['content'];
        
        foreach ($actorNames as $actorName) {
            if (str_contains($textToSearch, $actorName)) {
                $actor = Person::where('name', 'LIKE', '%' . $actorName . '%')->first();
                if ($actor && !in_array($actor->id, $attachedActors)) {
                    $news->people()->attach($actor->id, [
                        'primary_subject' => count($attachedActors) == 0
                    ]);
                    $attachedActors[] = $actor->id;
                }
            }
        }
        
        return count($attachedActors);
    }
}