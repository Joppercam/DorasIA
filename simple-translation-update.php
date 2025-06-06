<?php
/**
 * Script simple para traducir contenido básico sin APIs externas
 * Ejecutar desde la raíz del proyecto: php simple-translation-update.php
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Series;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🌐 DORASIA - Actualización de traducciones simples\n";
echo "===============================================\n\n";

// Diccionario básico de traducciones
$translations = [
    // Títulos comunes
    'Love' => 'Amor',
    'Heart' => 'Corazón',
    'Secret' => 'Secreto',
    'Beautiful' => 'Hermoso',
    'Dream' => 'Sueño',
    'Princess' => 'Princesa',
    'King' => 'Rey',
    'Queen' => 'Reina',
    'Hospital' => 'Hospital',
    'Doctor' => 'Doctor',
    'School' => 'Escuela',
    'High School' => 'Escuela Secundaria',
    'Romance' => 'Romance',
    'Story' => 'Historia',
    'Tale' => 'Cuento',
    'Life' => 'Vida',
    'Time' => 'Tiempo',
    'City' => 'Ciudad',
    'Night' => 'Noche',
    'Day' => 'Día',
    'Moon' => 'Luna',
    'Star' => 'Estrella',
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
    'Age' => 'Edad',
    'Again' => 'De Nuevo',
    'Return' => 'Regreso',
    'Come Back' => 'Regresa',
    'Goodbye' => 'Adiós',
    'Hello' => 'Hola',
    'Miss' => 'Señorita',
    'Mister' => 'Señor',
    'Strong' => 'Fuerte',
    'Weak' => 'Débil',
    'Lucky' => 'Afortunado',
    'Happy' => 'Feliz',
    'Sad' => 'Triste',
    'Angry' => 'Enojado',
    'Crazy' => 'Loco',
    'Mad' => 'Loco',
    'Sweet' => 'Dulce',
    'Bitter' => 'Amargo',
    'Hot' => 'Caliente',
    'Cold' => 'Frío',
    'Fire' => 'Fuego',
    'Water' => 'Agua',
    'Sky' => 'Cielo',
    'Earth' => 'Tierra',
    'World' => 'Mundo',
    'Universe' => 'Universo',
    'Space' => 'Espacio',
    'Magic' => 'Magia',
    'Miracle' => 'Milagro',
    'Angel' => 'Ángel',
    'Devil' => 'Diablo',
    'God' => 'Dios',
    'Spirit' => 'Espíritu',
    'Soul' => 'Alma',
    'Ghost' => 'Fantasma',
    'Monster' => 'Monstruo',
    'Hero' => 'Héroe',
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
    'Artist' => 'Artista',
    'Singer' => 'Cantante',
    'Actor' => 'Actor',
    'Actress' => 'Actriz',
    'Model' => 'Modelo',
    'Fashion' => 'Moda',
    'Style' => 'Estilo',
    'Beauty' => 'Belleza',
    'Beast' => 'Bestia',
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
    'Bike' => 'Bicicleta',
    'Walk' => 'Caminar',
    'Run' => 'Correr',
    'Jump' => 'Saltar',
    'Dance' => 'Bailar',
    'Sing' => 'Cantar',
    'Play' => 'Jugar',
    'Game' => 'Juego',
    'Sport' => 'Deporte',
    'Team' => 'Equipo',
    'Match' => 'Partido',
    'Win' => 'Ganar',
    'Lose' => 'Perder',
    'Fight' => 'Pelear',
    'War' => 'Guerra',
    'Peace' => 'Paz',
    'Love Story' => 'Historia de Amor',
    'True Love' => 'Amor Verdadero',
    'First Love' => 'Primer Amor',
    'Last Love' => 'Último Amor',
    'Secret Love' => 'Amor Secreto',
    'Fake Love' => 'Amor Falso',
    'Real Love' => 'Amor Real',
    'Pure Love' => 'Amor Puro',
    'Sweet Love' => 'Amor Dulce',
    'Bitter Love' => 'Amor Amargo',
    'Lost Love' => 'Amor Perdido',
    'Found Love' => 'Amor Encontrado'
];

function translateText($text, $translations) {
    $result = $text;
    
    // Traducir palabras completas primero (más específicas)
    foreach ($translations as $english => $spanish) {
        // Buscar la palabra completa (no como parte de otra palabra)
        $pattern = '/\b' . preg_quote($english, '/') . '\b/i';
        $result = preg_replace($pattern, $spanish, $result);
    }
    
    return $result;
}

echo "📊 Obteniendo series sin traducciones...\n";

try {
    $seriesToUpdate = Series::where(function($query) {
        $query->whereNull('title_es')
              ->orWhereNull('overview_es')
              ->orWhere('title_es', '')
              ->orWhere('overview_es', '');
    })->take(100)->get();

    echo "   📈 Encontradas {$seriesToUpdate->count()} series para actualizar\n\n";

    $updated = 0;
    foreach ($seriesToUpdate as $series) {
        echo "   📺 Procesando: {$series->title}\n";
        
        $changes = false;
        
        // Traducir título si no existe
        if (empty($series->title_es) && !empty($series->title)) {
            $translatedTitle = translateText($series->title, $translations);
            if ($translatedTitle !== $series->title) {
                $series->title_es = $translatedTitle;
                $changes = true;
                echo "      ✅ Título: {$translatedTitle}\n";
            } else {
                // Si no hay traducción automática, usar el título original
                $series->title_es = $series->title;
                $changes = true;
            }
        }
        
        // Traducir overview si no existe
        if (empty($series->overview_es) && !empty($series->overview)) {
            $translatedOverview = translateText($series->overview, $translations);
            if ($translatedOverview !== $series->overview) {
                $series->overview_es = $translatedOverview;
                $changes = true;
                echo "      ✅ Sinopsis traducida\n";
            } else {
                // Si no hay traducción automática, usar el overview original
                $series->overview_es = $series->overview;
                $changes = true;
            }
        }
        
        if ($changes) {
            $series->save();
            $updated++;
            echo "      💾 Guardado\n";
        }
        
        echo "\n";
    }

    echo "🎉 Proceso completado!\n";
    echo "   📊 Series actualizadas: {$updated}\n";
    echo "   🌐 Las traducciones están ahora disponibles\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n💡 Siguiente paso:\n";
echo "   🚀 Ejecutar: git add . && git commit -m 'Agregar traducciones' && git push\n";
echo "   📱 Luego en el hosting: git pull origin main\n\n";