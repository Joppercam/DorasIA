<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use Carbon\Carbon;

class NewsSeeder extends Seeder
{
    public function run()
    {
        $news = [
            [
                'title' => 'Los K-Dramas más esperados de 2025',
                'excerpt' => 'Descubre cuáles son las producciones coreanas que llegan este año con las mejores historias y actores.',
                'content' => '<p>El 2025 promete ser un año increíble para los fanáticos de los K-Dramas. Con producciones que van desde romance hasta thriller, la industria coreana continúa sorprendiendo al mundo entero.</p><p>Entre los títulos más esperados encontramos nuevas colaboraciones de actores reconocidos, historias originales que prometen emocionar y producciones de alto presupuesto que marcarán un antes y un después en la televisión coreana.</p><p>Los géneros que dominarán este año incluyen el romance contemporáneo, los dramas históricos (sageuk) y las series de misterio con elementos sobrenaturales.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800&h=400&fit=crop',
                'category' => 'estrenos',
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(1),
            ],
            [
                'title' => 'Análisis: El fenómeno global de los K-Dramas',
                'excerpt' => 'Un vistazo profundo a cómo las series coreanas conquistaron el mundo y cambiaron la industria del entretenimiento.',
                'content' => '<p>Los K-Dramas han experimentado un crecimiento exponencial en popularidad a nivel mundial. Desde "Winter Sonata" hasta "Squid Game", las producciones coreanas han logrado cautivar audiencias de todos los continentes.</p><p>Este fenómeno no es casualidad. La combinación única de narrativas emotivas, valores culturales profundos y producción de alta calidad ha creado un producto de entretenimiento distintivo que resuena con audiencias globales.</p><p>En Chile, el amor por los K-Dramas ha crecido exponencialmente, especialmente entre las nuevas generaciones que encuentran en estas historias una forma fresca de ver el amor, la familia y los desafíos de la vida moderna.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1594736797933-d0df8e04b2a6?w=800&h=400&fit=crop',
                'category' => 'analisis',
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(3),
            ],
            [
                'title' => 'Top 10: Los mejores actores de K-Drama de la década',
                'excerpt' => 'Conoce a los intérpretes que han marcado la industria del drama coreano en los últimos 10 años.',
                'content' => '<p>La última década ha sido testigo del surgimiento de actores extraordinarios que han redefinido el panorama de los K-Dramas. Desde veteranos que han perfeccionado su arte hasta nuevos talentos que han tomado por asalto la industria.</p><p>En esta lista encontrarás tanto a los galanes románticos que han hecho suspirar a millones, como a los actores de carácter que han dado vida a algunos de los personajes más memorables de la televisión coreana.</p><p>Cada uno de estos artistas ha contribuido de manera única al éxito global de los K-Dramas, llevando la actuación coreana a nuevas alturas de reconocimiento internacional.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=400&fit=crop',
                'category' => 'actores',
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(5),
            ],
            [
                'title' => 'Guía completa: Cómo entender la cultura coreana a través de los K-Dramas',
                'excerpt' => 'Los K-Dramas son ventanas perfectas para conocer las tradiciones, valores y modernidad de Corea del Sur.',
                'content' => '<p>Los K-Dramas ofrecen mucho más que entretenimiento; son ventanas auténticas a la cultura coreana contemporánea. A través de estas series, los espectadores pueden aprender sobre jerarquías sociales, tradiciones familiares, el sistema educativo y las dinámicas laborales de Corea del Sur.</p><p>Conceptos como el "jeong" (vínculos emocionales profundos), el respeto por los mayores, y la importancia de la educación y el trabajo duro son temas recurrentes que reflejan valores culturales genuinos.</p><p>Esta guía te ayudará a identificar y comprender estos elementos culturales para disfrutar aún más de tus K-Dramas favoritos.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800&h=400&fit=crop',
                'category' => 'cultura',
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(7),
            ],
            [
                'title' => 'Los K-Dramas históricos más impresionantes de todos los tiempos',
                'excerpt' => 'Un recorrido por los sageuks más épicos que han llevado la historia coreana a la pantalla.',
                'content' => '<p>Los dramas históricos coreanos, conocidos como "sageuk", representan algunas de las producciones más ambiciosas y visualmente impresionantes de la televisión coreana. Estas series transportan a los espectadores a diferentes dinastías y períodos históricos de Corea.</p><p>Desde las intrigas palaciegas de la dinastía Joseon hasta las luchas por el poder en reinos antiguos, los sageuks combinan historia, romance, acción y drama político en narrativas épicas que pueden extenderse por docenas de episodios.</p><p>Producciones como "Jumong", "The Empress Ki" y "Mr. Sunshine" han establecido estándares de calidad que continúan inspirando nuevas obras maestras del género histórico.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800&h=400&fit=crop',
                'category' => 'historicos',
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(10),
            ],
            [
                'title' => 'Tendencias 2025: Los géneros de K-Drama que están dominando',
                'excerpt' => 'Descubre qué tipos de historias están capturando la atención de las audiencias este año.',
                'content' => '<p>El 2025 está marcado por la evolución y diversificación de los géneros en los K-Dramas. Mientras que el romance sigue siendo un pilar fundamental, hemos visto un aumento significativo en thrillers psicológicos, series de fantasía urbana y dramas que abordan temas sociales contemporáneos.</p><p>Los K-Dramas médicos y legales han ganado popularidad, ofreciendo narrativas más maduras y complejas. Además, las series que mezclan elementos sobrenaturales con drama romántico están creando nuevos subgéneros que cautivan a audiencias globales.</p><p>Esta diversificación refleja la madurez de la industria y su capacidad para innovar mientras mantiene los elementos emocionales que han hecho famosos a los K-Dramas.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1594736797933-d0df8e04b2a6?w=800&h=400&fit=crop',
                'category' => 'tendencias',
                'is_featured' => true,
                'published_at' => Carbon::now()->subHours(12),
            ],
            [
                'title' => 'Entrevista exclusiva: Directores revelan secretos de producción',
                'excerpt' => 'Conversamos con reconocidos directores sobre las técnicas cinematográficas que hacen únicos a los K-Dramas.',
                'content' => '<p>En una entrevista exclusiva, varios directores de renombre compartieron insights únicos sobre el proceso creativo detrás de los K-Dramas más exitosos. Desde la planificación de escenas emotivas hasta la dirección de actores en momentos intensos.</p><p>Los directores enfatizan la importancia del detalle en cada toma, explicando cómo pequeños gestos y expresiones faciales pueden transmitir emociones profundas que conectan con audiencias de diferentes culturas.</p><p>También discutieron las innovaciones técnicas que están implementando para crear experiencias visuales más inmersivas, incluyendo el uso de drones para tomas aéreas espectaculares y técnicas de iluminación que realzan la belleza natural de los actores.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?w=800&h=400&fit=crop',
                'category' => 'entrevistas',
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(2),
            ],
            [
                'title' => 'OSTs que conquistaron 2024: Las mejores bandas sonoras del año',
                'excerpt' => 'Un repaso por las canciones que nos emocionaron y se convirtieron en hits mundiales.',
                'content' => '<p>Las bandas sonoras de los K-Dramas han evolucionado hasta convertirse en fenómenos musicales independientes. Este año hemos sido testigos de OSTs que no solo complementan las series, sino que se han posicionado en charts musicales internacionales.</p><p>Artistas como IU, Davichi y Park Hyo-shin han creado melodías inolvidables que trascienden las barreras del idioma. Sus voces emotivas han logrado transmitir los sentimientos más profundos de los personajes.</p><p>Analizamos las tendencias musicales en los OSTs actuales, desde baladas melancólicas hasta temas pop energéticos que reflejan la diversidad emocional de las historias contemporáneas.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=800&h=400&fit=crop',
                'category' => 'musica',
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(4),
            ],
            [
                'title' => 'Guía de locaciones: Lugares icónicos de tus K-Dramas favoritos',
                'excerpt' => 'Descubre dónde se filmaron las escenas más memorables y cómo visitarlas en tu próximo viaje a Corea.',
                'content' => '<p>Corea del Sur se ha convertido en un destino turístico imprescindible para los fanáticos de los K-Dramas. Desde el puente Banpo en Seúl hasta la isla de Jeju, las locaciones de filmación se han transformado en sitios de peregrinaje para admiradores de todo el mundo.</p><p>En esta guía completa, te llevamos por un recorrido virtual de los lugares más emblemáticos. Incluimos consejos prácticos para visitar cada locación, los mejores momentos del día para fotografías, y datos curiosos sobre las filmaciones.</p><p>También exploramos locaciones menos conocidas pero igualmente hermosas, desde cafeterías pintorescas en barrios tradicionales hasta paisajes naturales impresionantes que han servido de escenario para romances inolvidables.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1517154421773-0529f29ea451?w=800&h=400&fit=crop',
                'category' => 'turismo',
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(6),
            ]
        ];

        foreach ($news as $item) {
            News::create($item);
        }
    }
}