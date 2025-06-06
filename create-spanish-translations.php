<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🇪🇸 AGREGANDO TRADUCCIONES AL ESPAÑOL 🇪🇸\n";
echo "=========================================\n\n";

// Mapeo de títulos de K-Dramas famosos al español
$titleTranslations = [
    // Romance populares
    'Crash Landing on You' => 'Aterrizaje de Emergencia en tu Corazón',
    'Descendants of the Sun' => 'Descendientes del Sol',
    'What\'s Wrong with Secretary Kim' => '¿Qué le Pasa a la Secretaria Kim?',
    'Strong Woman Do Bong Soon' => 'Mujer Fuerte Do Bong Soon',
    'Weightlifting Fairy Kim Bok-joo' => 'El Hada de las Pesas Kim Bok-joo',
    'My Love From Another Star' => 'Mi Amor de las Estrellas',
    'The Heirs' => 'Los Herederos',
    'Boys Over Flowers' => 'Chicos Antes que Flores',
    'Secret Garden' => 'Jardín Secreto',
    'Coffee Prince' => 'Príncipe del Café',
    'My Girlfriend Is a Gumiho' => 'Mi Novia es una Gumiho',
    'Personal Taste' => 'Gusto Personal',
    'Full House' => 'Casa Llena',
    'You Are Beautiful' => 'Eres Hermosa',
    'Playful Kiss' => 'Beso Travieso',
    'Winter Sonata' => 'Sonata de Invierno',
    'Spring Waltz' => 'Vals de Primavera',
    'Summer Scent' => 'Aroma de Verano',
    'Autumn in My Heart' => 'Otoño en Mi Corazón',
    
    // Dramas históricos/Sageuk
    'The Moon Embracing the Sun' => 'La Luna que Abraza al Sol',
    'Moon Lovers: Scarlet Heart Ryeo' => 'Amantes de la Luna: Corazón Escarlata Ryeo',
    'Empress Ki' => 'La Emperatriz Ki',
    'Jewel in the Palace' => 'La Joya del Palacio',
    'Queen Seondeok' => 'La Reina Seondeok',
    'Hwarang: The Poet Warrior Youth' => 'Hwarang: Los Jóvenes Guerreros Poetas',
    'The King: Eternal Monarch' => 'El Rey: Monarca Eterno',
    'Mr. Sunshine' => 'Sr. Sunshine',
    'Goblin' => 'Duende',
    'Hotel Del Luna' => 'Hotel del Luna',
    
    // Thriller/Acción
    'Kingdom' => 'Reino',
    'Signal' => 'Señal',
    'Voice' => 'Voz',
    'Stranger' => 'Extraño',
    'The Guest' => 'El Huésped',
    'Taxi Driver' => 'Conductor de Taxi',
    'Mouse' => 'Ratón',
    'Squid Game' => 'El Juego del Calamar',
    'All of Us Are Dead' => 'Estamos Todos Muertos',
    'Sweet Home' => 'Dulce Hogar',
    'My Name' => 'Mi Nombre',
    'Hellbound' => 'Rumbo al Infierno',
    
    // Comedia/Drama
    'Hospital Playlist' => 'Lista de Reproducción del Hospital',
    'Prison Playbook' => 'Manual de la Prisión',
    'Reply 1988' => 'Respuesta 1988',
    'Prison Playbook' => 'Manual de la Prisión',
    'When the Camellia Blooms' => 'Cuando Florece la Camelia',
    'Hometown Cha-Cha-Cha' => 'Pueblo Cha-Cha-Cha',
    'Be Melodramatic' => 'Sé Melodramática',
    'Because This Is My First Life' => 'Porque Esta es Mi Primera Vida',
    'Fight For My Way' => 'Lucha por Mi Camino',
    'Chief Kim' => 'Jefe Kim',
    'Misaeng' => 'Vida Incompleta',
    
    // Médicos
    'Doctor Stranger' => 'Doctor Extraño',
    'Good Doctor' => 'Buen Doctor',
    'Doctors' => 'Doctores',
    'Dr. Romantic' => 'Dr. Romántico',
    'Emergency Couple' => 'Pareja de Emergencia',
    
    // Escuela/Juventud
    'School 2015' => 'Escuela 2015',
    'School 2017' => 'Escuela 2017',
    'Dream High' => 'Sueña Alto',
    'True Beauty' => 'Belleza Verdadera',
    'Extraordinary You' => 'Extraordinaria Tú',
    'Love Alarm' => 'Alarma de Amor',
    'My First First Love' => 'Mi Primer Primer Amor',
    'A Love So Beautiful' => 'Un Amor Tan Hermoso',
    
    // Fantasía
    'W: Two Worlds' => 'W: Dos Mundos',
    'While You Were Sleeping' => 'Mientras Dormías',
    'I Hear Your Voice' => 'Escucho Tu Voz',
    'Pinocchio' => 'Pinocho',
    'City Hunter' => 'Cazador de la Ciudad',
    'Healer' => 'Sanador',
    'The K2' => 'El K2',
    'Iris' => 'Iris'
];

// Mapeo de géneros al español
$genreTranslations = [
    'Drama' => 'Drama',
    'Comedy' => 'Comedia',
    'Romance' => 'Romance',
    'Action & Adventure' => 'Acción y Aventura',
    'Mystery' => 'Misterio',
    'Thriller' => 'Thriller',
    'Crime' => 'Crimen',
    'Sci-Fi & Fantasy' => 'Ciencia Ficción y Fantasía',
    'Horror' => 'Terror',
    'Family' => 'Familiar',
    'War & Politics' => 'Guerra y Política',
    'Documentary' => 'Documental',
    'Reality' => 'Reality',
    'Talk' => 'Talk Show',
    'News' => 'Noticias',
    'Kids' => 'Infantil',
    'Soap' => 'Telenovela',
    'Animation' => 'Animación'
];

try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/database/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "📝 Actualizando títulos de series...\n";
    $updatedTitles = 0;
    
    foreach ($titleTranslations as $originalTitle => $spanishTitle) {
        $stmt = $pdo->prepare("UPDATE series SET title_es = ? WHERE title = ? OR original_title = ?");
        $result = $stmt->execute([$spanishTitle, $originalTitle, $originalTitle]);
        if ($stmt->rowCount() > 0) {
            echo "✅ {$originalTitle} → {$spanishTitle}\n";
            $updatedTitles += $stmt->rowCount();
        }
    }
    
    echo "\n📂 Actualizando géneros...\n";
    $updatedGenres = 0;
    
    foreach ($genreTranslations as $originalGenre => $spanishGenre) {
        $stmt = $pdo->prepare("UPDATE genres SET name_es = ? WHERE name = ?");
        $result = $stmt->execute([$spanishGenre, $originalGenre]);
        if ($stmt->rowCount() > 0) {
            echo "✅ {$originalGenre} → {$spanishGenre}\n";
            $updatedGenres += $stmt->rowCount();
        }
    }
    
    // Traducciones de sinopsis comunes (patrones)
    echo "\n📖 Agregando traducciones de sinopsis comunes...\n";
    $synopsisPatterns = [
        'A young woman' => 'Una joven mujer',
        'A young man' => 'Un joven hombre',
        'falls in love' => 'se enamora',
        'love story' => 'historia de amor',
        'romantic comedy' => 'comedia romántica',
        'high school' => 'escuela secundaria',
        'medical drama' => 'drama médico',
        'historical drama' => 'drama histórico',
        'mystery thriller' => 'thriller de misterio',
        'family drama' => 'drama familiar'
    ];
    
    $synopsisUpdated = 0;
    foreach ($synopsisPatterns as $english => $spanish) {
        $stmt = $pdo->prepare("UPDATE series SET overview_es = REPLACE(COALESCE(overview_es, overview), ?, ?) WHERE overview LIKE ?");
        $result = $stmt->execute([$english, $spanish, "%{$english}%"]);
        $synopsisUpdated += $stmt->rowCount();
    }
    
    echo "📊 RESUMEN DE TRADUCCIONES:\n";
    echo "─────────────────────────\n";
    echo "🎬 Títulos traducidos: {$updatedTitles}\n";
    echo "📂 Géneros traducidos: {$updatedGenres}\n";
    echo "📖 Sinopsis mejoradas: {$synopsisUpdated}\n\n";
    
    echo "✨ ¡Traducciones aplicadas exitosamente!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

?>