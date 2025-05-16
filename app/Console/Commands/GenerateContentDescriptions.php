<?php

namespace App\Console\Commands;

use App\Models\Title;
use Illuminate\Console\Command;

class GenerateContentDescriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dorasia:generate-descriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Spanish descriptions for all titles';

    /**
     * Mapeo de descripciones para títulos populares
     */
    protected $descriptions = [
        // Descripciones en español para dramas coreanos
        'No Stop' => 'Una comedia universitaria que sigue las vidas de estudiantes mientras navegan por el amor, la amistad y los desafíos académicos. Con humor y situaciones cotidianas, la serie muestra el crecimiento personal de jóvenes adultos en su camino hacia la madurez.',
        
        'Mirar y mirar otra vez' => 'Una historia de amor que pone a prueba la idea de que las segundas oportunidades pueden ser más poderosas que el primer encuentro. Cuando dos personas destinadas a estar juntas se reencuentran años después, descubren que sus sentimientos han madurado con el tiempo.',
        
        'La Sirenita' => 'Inspirada en el clásico cuento, esta adaptación coreana sigue a una misteriosa mujer con un secreto del mar que se enamora de un hombre de la ciudad. Su romance desafía las barreras entre dos mundos completamente diferentes.',
        
        'Me gustas' => 'Una dulce historia romántica sobre un primer amor que florece en la escuela secundaria. Con momentos tiernos y situaciones conmovedoras, la serie captura la pureza e intensidad de las emociones adolescentes.',
        
        'El juego del calamar' => 'En un juego mortal donde el premio es una fortuna y el castigo es la muerte, cientos de jugadores endeudados aceptan una extraña invitación a competir en juegos infantiles. Lo que comienza como una oportunidad se convierte en una lucha desesperada por sobrevivir.',
        
        // Descripciones en español para dramas japoneses
        'Más azul que el índigo' => 'Ambientada en un pintoresco pueblo costero, esta emotiva historia sigue a una joven que regresa a su hogar para reconectarse con sus raíces. A través de relaciones con amigos de la infancia y nuevos conocidos, redescubre el significado del verdadero amor y la pertenencia.',
        
        'Tu nombre' => 'Una conmovedora historia que sigue a una familia mientras enfrentan los altibajos de la vida cotidiana. Con momentos de alegría y tristeza, la serie examina la importancia de la identidad personal y los lazos familiares a través de las generaciones.',
        
        'El mar de Hatoko' => 'Ambientada en una pequeña isla pesquera, esta nostálgica historia sigue la vida de Hatoko, una mujer que regresa a su pueblo natal tras años en la ciudad. Entre las olas del mar que la vio crecer, redescubrirá sus raíces y sanará viejas heridas.',
        
        'El viento de mañana' => 'En medio de los cambios sociales de la década de 1960, una familia se adapta a los nuevos tiempos mientras mantiene sus tradiciones. A través de alegrías y dificultades, el viento de cambio sopla hacia el futuro prometedor de una nueva generación.',
        
        'Oshin' => 'La extraordinaria historia de una mujer que supera increíbles adversidades durante la transformación de Japón desde la era Meiji hasta la posguerra. Nacida en extrema pobreza, Oshin demuestra una inquebrantable perseverancia y dignidad a lo largo de su vida.',
        
        'Tamayura' => 'Tras la muerte de su padre, una joven redescubre su pasión por la fotografía, arte que compartía con él. Mudándose a la pintoresca ciudad costera donde pasaba los veranos de su infancia, encuentra sanación y nuevas amistades que la ayudarán a seguir adelante.',
        
        'Honmamon' => 'Una cálida historia familiar que celebra la autenticidad y la tradición. Ambientada en Osaka, la serie sigue a una familia que mantiene un pequeño negocio tradicional mientras se adapta a la vida moderna, destacando la importancia de permanecer fiel a uno mismo.',
        
        'Ohanahan' => 'Ambientada en el período Meiji, esta historia sigue a una mujer fuerte y determinada mientras navega por un Japón en rápida modernización. Entre las tradiciones del pasado y las promesas del futuro, Ohanahan encuentra su propio camino hacia la independencia.',
        
        '¡Corre!' => 'Una inspiradora historia sobre perseverancia y superación personal. Cuando una joven con un talento natural para correr decide perseguir sus sueños, deberá enfrentarse no solo a competidores, sino también a sus propias dudas y a las expectativas de su familia.',
        
        'El estudio fotográfico de Natchan' => 'En un pequeño estudio fotográfico de un barrio tradicional, Natchan captura los momentos más preciados de la vida de sus clientes. A través de su lente, presenciamos historias de amor, pérdida, alegría y transformación en la comunidad.',
        
        'Karin' => 'Una historia llena de ternura sobre una joven con un don especial para hacer felices a los demás. A medida que ayuda a quienes la rodean, Karin aprende valiosas lecciones sobre la amistad, el sacrificio y el verdadero significado de la felicidad.',
        
        'Hoy también está despejado' => 'En medio de las tormentas de la vida, esta serie nos recuerda que siempre hay días soleados por delante. Siguiendo a diversos personajes en un vecindario japonés, cada episodio entrelaza historias de superación personal y momentos de inesperada alegría.',
        
        'Chocchan' => 'Basada en la vida de la escritora Tomoe Chujo, esta conmovedora historia sigue a una niña extraordinaria en el Japón de principios del siglo XX. Con una curiosidad insaciable y un espíritu indomable, Chocchan desafía las expectativas sociales de su época.',
        
        'Lirio de los valles' => 'Una delicada historia sobre una joven cuya discreta belleza y bondad transforman la vida de quienes la rodean. Como la flor que le da nombre, su presencia silenciosa pero poderosa trae esperanza y sanación a un pueblo marcado por secretos del pasado.',
        
        // Descripciones en español para dramas chinos
        'Tornado de Taiwán' => 'Un apasionante drama familiar lleno de secretos, traiciones y reconciliaciones. Cuando un evento inesperado sacude los cimientos de una familia aparentemente perfecta, las verdades ocultas emergen como un tornado, transformando para siempre las vidas de todos los involucrados.',
        
        // Otros títulos populares
        'Amor en el pabellón' => 'Ambientada en la antigua China, esta historia de amor prohibido entre un príncipe y una doncella de palacio desafía las rígidas normas sociales de la época. Entre intrigas políticas y tradiciones ancestrales, su romance florecerá como una delicada flor en el imponente pabellón imperial.',
        
        'Si la vida te da mandarinas...' => 'Una refrescante comedia romántica que sigue a una optimista productora de cítricos de una pequeña isla surcoreana. Cuando un ejecutivo de Seúl llega para adquirir sus tierras, comienza una batalla de voluntades que podría transformarse en algo más dulce que el jugo de mandarina.',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generando descripciones en español para los títulos...');
        
        // Obtener todos los títulos
        $titles = Title::all();
        $updated = 0;
        
        $this->output->progressStart($titles->count());
        
        foreach ($titles as $title) {
            // Si el título tiene una descripción mapeada, usarla
            if (isset($this->descriptions[$title->title])) {
                $title->description = $this->descriptions[$title->title];
                $title->save();
                $updated++;
                $this->line("Descripción actualizada para: {$title->title}");
            } 
            // Si no hay descripción específica pero el título no tiene descripción, generar una genérica
            elseif (empty($title->description)) {
                // Determinar el tipo de contenido (K-Drama, J-Drama, etc.)
                $type = 'drama asiático';
                if ($title->category_id == 1) {
                    $type = 'drama coreano';
                } elseif ($title->category_id == 2) {
                    $type = 'drama japonés';
                } elseif ($title->category_id == 3) {
                    $type = 'drama chino';
                } elseif ($title->category_id == 4) {
                    $type = 'drama tailandés';
                }
                
                // Obtener los géneros si están disponibles
                $genreNames = [];
                if ($title->genres) {
                    foreach ($title->genres as $genre) {
                        $genreNames[] = strtolower($genre->name);
                    }
                }
                
                // Elegir descripción según el género
                if (in_array('romance', $genreNames)) {
                    $title->description = "Una cautivadora historia de amor que transcurre en el contexto de un {$type}. Con momentos emotivos y giros inesperados, esta serie nos muestra que el amor verdadero puede superar cualquier obstáculo.";
                } elseif (in_array('comedia', $genreNames)) {
                    $title->description = "Una divertida comedia que te hará reír con sus situaciones hilarantes y personajes carismáticos. Este {$type} mezcla humor y ternura para ofrecer una experiencia única llena de momentos memorables.";
                } elseif (in_array('drama', $genreNames) || in_array('melodrama', $genreNames)) {
                    $title->description = "Un intenso drama que explora las complejidades de las relaciones humanas y las decisiones difíciles que enfrentamos en la vida. Esta conmovedora historia te atrapará con su profundidad emocional y personajes auténticos.";
                } elseif (in_array('histórico', $genreNames)) {
                    $title->description = "Un fascinante {$type} histórico que te transportará a otra época. Con una minuciosa recreación del periodo, esta serie combina hechos históricos con una narrativa cautivadora que te mantendrá al borde de tu asiento.";
                } elseif (in_array('fantasía', $genreNames) || in_array('sobrenatural', $genreNames)) {
                    $title->description = "Un extraordinario {$type} que mezcla elementos fantásticos con una historia cautivadora. Adentrándose en lo sobrenatural, esta serie desafía los límites entre la realidad y la fantasía.";
                } elseif (in_array('acción', $genreNames) || in_array('thriller', $genreNames)) {
                    $title->description = "Un trepidante {$type} lleno de acción y suspenso que te mantendrá al borde de tu asiento. Con giros inesperados y escenas impactantes, cada episodio es una montaña rusa de emociones.";
                } else {
                    // Descripción genérica si no se puede determinar el género
                    $title->description = "Una fascinante serie que captura la esencia de la cultura asiática con su narrativa única y personajes memorables. Este {$type} te sumergirá en un mundo de emociones y te hará experimentar una amplia gama de sentimientos.";
                }
                
                $title->save();
                $updated++;
                $this->line("Descripción genérica generada para: {$title->title}");
            }
            
            $this->output->progressAdvance();
        }
        
        $this->output->progressFinish();
        $this->info("Se han actualizado las descripciones de {$updated} títulos.");
        
        return Command::SUCCESS;
    }
}