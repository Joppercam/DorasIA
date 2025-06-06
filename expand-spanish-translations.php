<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Series;
use Illuminate\Support\Facades\DB;

echo "🇪🇸 Expandiendo traducciones al español...\n";

// Traducciones expandidas de series populares
$translations = [
    // Series más populares
    'Squid Game' => [
        'title_es' => 'El Juego del Calamar',
        'overview_es' => 'Cientos de jugadores con problemas de dinero aceptan una extraña invitación a competir en juegos infantiles. Dentro les espera un premio tentador, con un riesgo mortal.',
        'synopsis_es' => 'Seong Gi-hun, un hombre divorciado y con deudas, recibe una invitación para participar en un misterioso juego. Junto a otros 455 participantes desesperados por dinero, se ve envuelto en una competencia mortal donde juegos infantiles se convierten en una lucha por la supervivencia con un premio de 45.6 mil millones de wones.',
        'tagline_es' => 'Un juego mortal por la supervivencia'
    ],
    'Crash Landing on You' => [
        'title_es' => 'Aterrizaje de Emergencia en tu Corazón',
        'overview_es' => 'Una heredera surcoreana aterriza accidentalmente en Corea del Norte tras un accidente de parapente y conoce a un oficial del ejército que la ayuda a esconderse.',
        'synopsis_es' => 'Yoon Se-ri, una exitosa empresaria surcoreana, sufre un accidente de parapente que la lleva a aterrizar en Corea del Norte. Allí conoce a Ri Jeong-hyeok, un oficial del ejército norcoreano que decide ayudarla a regresar a casa mientras oculta su presencia. Lo que comienza como una situación de supervivencia se convierte en una hermosa historia de amor que trasciende fronteras.',
        'tagline_es' => 'Un amor que trasciende fronteras'
    ],
    'Goblin' => [
        'title_es' => 'Goblin: El Solitario y Gran Dios',
        'overview_es' => 'Un goblin inmortal busca a su novia humana para poner fin a su vida inmortal, pero se enamora de ella inesperadamente.',
        'synopsis_es' => 'Kim Shin es un goblin inmortal que ha vivido durante 939 años buscando a la novia humana destinada a sacar la espada de su pecho y liberar su alma. Cuando finalmente la encuentra en Ji Eun-tak, una estudiante de secundaria optimista que puede ver fantasmas, descubre que el amor puede ser más poderoso que su deseo de muerte. Junto al Dios de la Muerte, quien perdió sus memorias, viven juntos en una extraña cohabitation.',
        'tagline_es' => 'Un amor inmortal que desafía el destino'
    ],
    'Descendants of the Sun' => [
        'title_es' => 'Descendientes del Sol',
        'overview_es' => 'Un capitán de las fuerzas especiales y una cirujana se enamoran, pero sus carreras y valores diferentes ponen a prueba su relación.',
        'synopsis_es' => 'El Capitán Yoo Si-jin, líder de las fuerzas especiales, conoce a la Dra. Kang Mo-yeon, una cirujana cardiotorácica, durante una misión de emergencia. A pesar de la química instantánea entre ellos, sus diferentes valores y carreras los separan. Cuando Si-jin es enviado a una misión de mantenimiento de paz en el país ficticio de Uruk, Mo-yeon también llega allí como parte de un equipo médico voluntario, reavivando su romance en medio de la zona de guerra.',
        'tagline_es' => 'Un amor que sobrevive en la zona de guerra'
    ],
    'Hotel Del Luna' => [
        'title_es' => 'Hotel del Luna',
        'overview_es' => 'Una CEO malhumorada de un hotel para fantasmas se ve obligada a trabajar con un gerente humano para resolver los asuntos pendientes de los huéspedes espirituales.',
        'synopsis_es' => 'Jang Man-wol es la CEO del Hotel del Luna, un hotel misterioso y elegante que alberga solo a huéspedes fantasmas. Maldecida a administrar el hotel durante más de 1000 años debido a los pecados de su pasado, conoce a Goo Chan-sung, un gerente hotelero élite que se ve obligado a trabajar en el hotel para pagar la deuda de su padre. Juntos ayudan a los espíritus a resolver sus asuntos pendientes antes de pasar al más allá.',
        'tagline_es' => 'Un hotel donde los fantasmas encuentran la paz'
    ],
    'It\'s Okay to Not Be Okay' => [
        'title_es' => 'Está Bien No Estar Bien',
        'overview_es' => 'Un cuidador de salud mental y una escritora de libros infantiles antisocial sanan las heridas emocionales del otro.',
        'synopsis_es' => 'Moon Gang-tae es un cuidador en un hospital psiquiátrico que vive para cuidar a su hermano mayor autista, renunciando a sus propios sueños y felicidad. Ko Moon-young es una exitosa pero antisocial escritora de libros infantiles que nunca conoció el amor debido a su trastorno de personalidad antisocial. Cuando sus caminos se cruzan, comienzan a sanarse mutuamente mientras enfrentan sus traumas del pasado y aprenden que está bien no estar bien.',
        'tagline_es' => 'Sanar juntos las heridas del corazón'
    ],
    'Business Proposal' => [
        'title_es' => 'Propuesta de Negocios',
        'overview_es' => 'Una empleada se hace pasar por su amiga en una cita a ciegas con un CEO, sin saber que es su jefe.',
        'synopsis_es' => 'Shin Ha-ri se ve obligada a sustituir a su amiga en una cita a ciegas fingiendo ser ella para ahuyentar al pretendiente. Sin embargo, el hombre resulta ser Kang Tae-moo, el CEO de la empresa donde trabaja. Para empeorar las cosas, él le propone matrimonio en el acto. Ha-ri debe mantener su identidad falsa mientras navega por una relación fake con su jefe millonario.',
        'tagline_es' => 'Una mentira que se convierte en amor'
    ],
    'Twenty Five Twenty One' => [
        'title_es' => 'Veinticinco Veintiuno',
        'overview_es' => 'En 1998, una esgrimista adolescente persigue sus sueños deportivos mientras se enamora en medio de la crisis financiera asiática.',
        'synopsis_es' => 'Na Hee-do es una estudiante de secundaria apasionada por la esgrima que se niega a rendirse ante las dificultades económicas de la crisis asiática de 1998. Cuando conoce a Baek Yi-jin, un joven de una familia adinerada que perdió todo en la crisis y ahora trabaja a tiempo parcial, comienzan una amistad que florece hacia el amor. La serie sigue sus sueños, luchas y el romance juvenil en una época de incertidumbre.',
        'tagline_es' => 'Los sueños juveniles nunca se rinden'
    ],
    'The Glory' => [
        'title_es' => 'La Gloria',
        'overview_es' => 'Una mujer ejecuta meticulosamente un plan de venganza contra sus acosadores escolares años después del trauma.',
        'synopsis_es' => 'Moon Dong-eun abandonó la escuela después de sufrir acoso extremo y violencia física de sus compañeros de clase. Años después, se convierte en maestra en la escuela primaria donde estudia la hija de su principal acosadora. Con paciencia y determinación, comienza a ejecutar su elaborado plan de venganza contra todos los que la torturaron, buscando no solo justicia sino también sanación para su alma destrozada.',
        'tagline_es' => 'La venganza es un plato que se sirve frío'
    ],
    'Kingdom' => [
        'title_es' => 'Reino',
        'overview_es' => 'En la Corea de la dinastía Joseon, un príncipe investiga una misteriosa plaga que convierte a las personas en zombis.',
        'synopsis_es' => 'El Príncipe Lee Chang descubre una conspiración mortal y una misteriosa plaga que está convirtiendo a los muertos en zombis hambrientos en la Corea de la dinastía Joseon. Mientras lucha por su trono y trata de salvar a su pueblo, debe enfrentar tanto a enemigos políticos como a hordas de no-muertos. La serie combina drama político, horror de zombis y espectaculares peleas de artes marciales en el contexto histórico de Corea.',
        'tagline_es' => 'Un reino bajo el terror de los muertos vivientes'
    ],
    'Vincenzo' => [
        'title_es' => 'Vincenzo',
        'overview_es' => 'Un abogado italo-coreano de la mafia regresa a Corea del Sur y se ve envuelto en una batalla legal contra una corporación corrupta.',
        'synopsis_es' => 'Vincenzo Cassano es un abogado italo-coreano y consigliere de la mafia italiana que regresa a Corea del Sur después de que una guerra entre familias mafiosas lo pone en peligro. Su plan de recuperar oro escondido en un edificio se complica cuando conoce a Hong Cha-young, una abogada apasionada pero despiadada. Juntos luchan contra Babel Group, una corporación malvada, usando métodos poco ortodoxos que combinan la justicia con la venganza.',
        'tagline_es' => 'La justicia a la manera de la mafia'
    ],
    'Extraordinary Attorney Woo' => [
        'title_es' => 'La Extraordinaria Abogada Woo',
        'overview_es' => 'Una brillante abogada joven con autismo navega por el mundo legal mientras enfrenta desafíos sociales y profesionales.',
        'synopsis_es' => 'Woo Young-woo es una abogada novata con autismo y un coeficiente intelectual de 164 que se gradúa con los mejores honores de la Universidad Nacional de Seúl. A pesar de su brillantez legal y memoria fotográfica, debe superar los prejuicios y desafíos sociales mientras maneja casos complejos en uno de los bufetes de abogados más prestigiosos de Corea. Con su perspectiva única, resuelve casos de maneras inesperadas mientras busca aceptación y amor.',
        'tagline_es' => 'Brillantez extraordinaria, corazón humano'
    ],
    'Strong Woman Do Bong Soon' => [
        'title_es' => 'La Mujer Fuerte Do Bong Soon',
        'overview_es' => 'Una joven con fuerza sobrehumana se convierte en guardaespaldas de un CEO mientras persigue a un secuestrador serial.',
        'synopsis_es' => 'Do Bong-soon nació con fuerza sobrehumana heredada de las mujeres de su familia, pero sueña con crear videojuegos y ser femenina. Cuando se convierte en guardaespaldas de Ahn Min-hyuk, el CEO narcisista de una empresa de videojuegos, debe protegerlo de las amenazas mientras ayuda a la policía a atrapar a un secuestrador serial en su vecindario. Entre entrenamientos, persecuciones y romance, Bong-soon aprende a aceptar su poder.',
        'tagline_es' => 'Poder femenino, corazón tierno'
    ],
    'My Love From Another Star' => [
        'title_es' => 'Mi Amor de Otra Estrella',
        'overview_es' => 'Un alienígena que ha vivido en la Tierra durante 400 años se enamora de una actriz famosa justo antes de regresar a su planeta.',
        'synopsis_es' => 'Do Min-joon es un alienígena que llegó a la Tierra durante la dinastía Joseon y ha vivido entre humanos durante 400 años. Con superpoderes como telekinesis, super fuerza y la capacidad de detener el tiempo, se ha mantenido distante de los humanos. Pero tres meses antes de regresar a su planeta, conoce a Cheon Song-yi, una actriz famosa y problemática que se parece a la mujer que amó en el pasado. Su amor prohibido desafía las leyes del universo.',
        'tagline_es' => 'Un amor que trasciende galaxias'
    ],
    'Reply 1988' => [
        'title_es' => 'Respuesta 1988',
        'overview_es' => 'Cinco amigos adolescentes y sus familias navegan por la vida en el barrio de Ssangmun-dong durante el año 1988.',
        'synopsis_es' => 'En 1988, en el barrio de Ssangmun-dong en Seúl, cinco amigos adolescentes crecen juntos mientras sus familias comparten risas, lágrimas y comidas. Sung Deok-sun y sus amigos experimentan el primer amor, la amistad, los sueños familiares y los pequeños momentos que hacen la vida significativa. La serie captura nostalgia y la calidez de los años 80 en Corea, cuando la vida era más simple pero igual de compleja emocionalmente.',
        'tagline_es' => 'Los mejores momentos de la juventud'
    ],
    'Start-Up' => [
        'title_es' => 'Empresa Emergente',
        'overview_es' => 'Jóvenes emprendedores persiguen sus sueños en el competitivo mundo de las startups tecnológicas de Corea del Sur.',
        'synopsis_es' => 'Seo Dal-mi sueña con convertirse en la Steve Jobs de Corea del Sur y crear su propia startup tecnológica. Cuando se reencuentra con Nam Do-san, el fundador de una startup en dificultades, y Han Ji-pyeong, un inversionista exitoso conectado a su pasado, se forma un triángulo amoroso. En el valle del silicio coreano llamado Sandbox, estos jóvenes luchan por hacer realidad sus sueños empresariales mientras navegan por el amor y la competencia.',
        'tagline_es' => 'Donde los sueños se convierten en realidad'
    ],
    'Hometown Cha-Cha-Cha' => [
        'title_es' => 'Cha-Cha-Cha del Pueblo',
        'overview_es' => 'Una dentista de Seúl se muda a un pueblo costero donde conoce a un hombre misterioso que ayuda a todos en la comunidad.',
        'synopsis_es' => 'Yoon Hye-jin es una dentista perfecta de Seúl que pierde su trabajo por defender a un paciente. Se muda al pueblo costero ficticio de Gongjin, donde conoce a Hong Du-sik, un hombre encantador que no tiene trabajo específico pero ayuda a todos en el pueblo con cualquier cosa que necesiten. Mientras Hye-jin se adapta a la vida del pueblo y Du-sik la ayuda a integrarse, ambos sanan heridas del pasado y encuentran el amor en el lugar más inesperado.',
        'tagline_es' => 'El amor florece junto al mar'
    ],
    'True Beauty' => [
        'title_es' => 'Belleza Verdadera',
        'overview_es' => 'Una estudiante de secundaria usa maquillaje para transformar su apariencia y esconder su verdadero rostro, pero se complica cuando se enamora.',
        'synopsis_es' => 'Lim Ju-gyeong es una estudiante de secundaria que sufre bullying por su apariencia hasta que descubre el poder del maquillaje. Con su nueva imagen, se convierte en la chica más popular de su nueva escuela. Mientras mantiene en secreto su verdadero rostro, se debate entre dos chicos: Lee Su-ho, el chico popular que accidentalmente descubre su secreto, y Han Seo-jun, el rebelde con corazón de oro. La serie explora temas de autoestima, belleza real y aceptación.',
        'tagline_es' => 'La belleza verdadera viene del corazón'
    ],
    'What\'s Wrong with Secretary Kim' => [
        'title_es' => 'Qué le Pasa a la Secretaria Kim',
        'overview_es' => 'Un CEO narcisista entra en pánico cuando su perfecta secretaria decide renunciar después de años de trabajo.',
        'synopsis_es' => 'Lee Young-joon es el vicepresidente narcisista de una gran corporación que cree firmemente en su propia perfección. Kim Mi-so ha sido su secretaria durante nueve años, manejando perfectamente todos los aspectos de su vida. Cuando Mi-so decide renunciar para vivir su propia vida, Young-joon hace todo lo posible para retenerla, incluso proponerle matrimonio. Mientras intenta descubrir por qué quiere irse, ambos confrontan memorias reprimidas de su infancia compartida.',
        'tagline_es' => 'Nueve años de dedicación, un momento de verdad'
    ],
    'Mr. Queen' => [
        'title_es' => 'Sr. Reina',
        'overview_es' => 'Un chef moderno despierta en el cuerpo de una reina de la dinastía Joseon y debe navegar por la política del palacio.',
        'synopsis_es' => 'Jang Bong-hwan es un chef playboy de la era moderna que, después de un accidente, despierta en el cuerpo de Kim So-yong, la reina de la dinastía Joseon. Ahora debe navegar por la vida del palacio, la política de la corte y un matrimonio con el Rey Cheoljong, mientras mantiene su personalidad moderna en un cuerpo de mujer del siglo XVIII. Con humor y drama, debe sobrevivir a los peligros del palacio mientras busca una manera de regresar a su tiempo.',
        'tagline_es' => 'Un alma moderna en un cuerpo real'
    ]
];

$updated = 0;
$total = count($translations);

foreach ($translations as $originalTitle => $translation) {
    $series = Series::where('title', $originalTitle)
                   ->orWhere('original_title', $originalTitle)
                   ->first();
    
    if ($series) {
        $series->update($translation);
        echo "✅ Actualizado: {$originalTitle} -> {$translation['title_es']}\n";
        $updated++;
    } else {
        echo "❌ No encontrado: {$originalTitle}\n";
    }
}

echo "\n🎉 Proceso completado!\n";
echo "📊 Series actualizadas: {$updated}/{$total}\n";
echo "🇪🇸 Todas las traducciones están listas para mostrar\n";