<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;
use App\Models\Series;

class FixSpanishContentQuick extends Command
{
    protected $signature = 'fix:spanish-content-quick';
    protected $description = 'Fix Spanish content with manual translations for common titles';

    public function handle()
    {
        $this->info('🇪🇸 Aplicando traducciones manuales...');

        $this->fixSeriesTitles();
        $this->fixMovieTitles();
        $this->fixGenericContent();

        $this->info('✅ Contenido en español actualizado');
        return 0;
    }

    private function fixSeriesTitles()
    {
        $seriesTranslations = [
            'Running Man' => 'Hombres en una Misión',
            'Infinite Challenge' => 'Desafío Infinito',
            '무한도전' => 'Desafío Infinito',
            '2 Days & 1 Night' => '2 Días y 1 Noche',
            'Return of Superman' => 'El regreso de superman',
            'The Genius' => 'El Genio',
            'Society Game' => 'Juego de Sociedad',
            'The Great Escape' => 'La Gran Fuga',
            'Busted!' => '¡Atrapados!',
            'Crime Scene' => 'Escena del Crimen',
            'Girls Generation' => 'Girls Generation',
            'Heart Signal' => 'Señal del Corazón',
            'Love Island' => 'Isla del Amor',
            'Single\'s Inferno' => 'El Infierno de los Solteros',
            'Physical: 100' => 'Físico: 100',
            'The Masked Singer' => 'El Cantante Enmascarado',
            'King of Masked Singer' => 'Rey del Cantante Enmascarado',
            'Immortal Songs' => 'Canciones Inmortales',
            'Amazing Saturday' => 'Sábado Increíble',
            'Knowing Brothers' => 'Hermanos Sabios',
            'Weekly Idol' => 'Ídolo Semanal',
            'Happy Together' => 'Felices Juntos',
            'Radio Star' => 'Estrella de Radio',
            'Three Meals a Day' => 'Tres Comidas al Día',
            'Youth Over Flowers' => 'Juventud Sobre Flores',
            'New Journey to the West' => 'Nuevo Viaje al Oeste',
            'Grandpas Over Flowers' => 'Abuelos Sobre Flores',
            'Hospital Playlist' => 'Lista de Reproducción del Hospital',
            'Reply 1988' => 'Respuesta 1988',
            'Reply 1997' => 'Respuesta 1997',
            'Reply 1994' => 'Respuesta 1994',
            'Prison Playbook' => 'Manual de la Prisión',
            'Because This Is My First Life' => 'Porque Esta Es Mi Primera Vida',
            'Something in the Rain' => 'Algo en la Lluvia',
            'One Spring Night' => 'Una Noche de Primavera',
            'My Mister' => 'Mi Señor',
            'Live' => 'En Vivo',
            'Misaeng' => 'Vida Incompleta',
            'Signal' => 'Señal',
            'Stranger' => 'Extraño',
            'Secret Forest' => 'Bosque Secreto',
            'Kingdom' => 'Reino',
            'Hellbound' => 'Destinado al Infierno',
            'All of Us Are Dead' => 'Estamos Todos Muertos',
            'Sweet Home' => 'Hogar Dulce Hogar',
            'Hometown\'s Embrace' => 'El Abrazo de Mi Pueblo',
            'Hometown Cha-Cha-Cha' => 'Mi Pueblo: Secretos en la Marea',
            'Start-Up' => 'Emprendimiento',
            'True Beauty' => 'Belleza Verdadera',
            'Record of Youth' => 'Registro de la Juventud',
            'Love Alarm' => 'Alarma de Amor',
            'My First First Love' => 'Mi Primer Primer Amor',
            'Romance is a Bonus Book' => 'El Romance es un Libro Extra',
            'Memories of the Alhambra' => 'Recuerdos de la Alhambra',
            'Mr. Sunshine' => 'Sr. Sunshine',
            'Arthdal Chronicles' => 'Crónicas de Arthdal',
            'The King: Eternal Monarch' => 'El Rey: Monarca Eterno',
            'Hotel del Luna' => 'Hotel del Luna',
            'Goblin' => 'Goblin: El Solitario Ser Inmortal',
            'Guardian: The Lonely and Great God' => 'Guardián: El Solitario y Gran Dios',
            'Descendants of the Sun' => 'Descendientes del Sol',
            'Moon Lovers: Scarlet Heart Ryeo' => 'Amantes de la Luna: Corazón Escarlata Ryeo',
            'Weightlifting Fairy Kim Bok-joo' => 'Hada del Levantamiento de Pesas Kim Bok-joo',
            'Strong Girl Bong-soon' => 'Chica Fuerte Bong-soon',
            'While You Were Sleeping' => 'Mientras Dormías',
            'W: Two Worlds' => 'W: Dos Mundos',
            'Doctors' => 'Doctores',
            'Pinocchio' => 'Pinocho',
            'The Heirs' => 'Los Herederos',
            'Boys Over Flowers' => 'Chicos Sobre Flores',
            'City Hunter' => 'Cazador de la Ciudad',
            'Secret Garden' => 'Jardín Secreto',
            'Coffee Prince' => 'Príncipe del Café',
            'My Girl' => 'Mi Chica',
            'Full House' => 'Casa Llena',
        ];

        foreach ($seriesTranslations as $original => $spanish) {
            Series::where(function($query) use ($original) {
                $query->where('title', $original)
                      ->orWhere('original_title', $original);
            })->update([
                'title_es' => $spanish
            ]);
        }

        $this->info('📺 Series titles updated');
    }

    private function fixMovieTitles()
    {
        $movieTranslations = [
            'Train to Busan' => 'Tren a Busan',
            'The Wailing' => 'El Lamento',
            'Oldboy' => 'Oldboy',
            'The Handmaiden' => 'La Doncella',
            'Burning' => 'Burning: Deseo Ardiente',
            'Parasite' => 'Parásitos',
            'Minari' => 'Minari',
            'Decision to Leave' => 'Decisión de Partir',
            'Drive My Car' => 'Drive My Car',
            'The Man from Nowhere' => 'El Hombre de Ninguna Parte',
            'I Saw the Devil' => 'Vi al Diablo',
            'The Chaser' => 'El Perseguidor',
            'Memories of Murder' => 'Memorias de un Asesino',
            'Mother' => 'Madre',
            'The Yellow Sea' => 'El Mar Amarillo',
            'A Taxi Driver' => 'Un Conductor de Taxi',
            '1987: When the Day Comes' => '1987: Cuando Llega el Día',
            'The Attorney' => 'El Abogado',
            'Ode to My Father' => 'Oda a Mi Padre',
            'Extreme Job' => 'Trabajo Extremo',
            'Exit' => 'Salida',
            'Midnight Runners' => 'Corredores de Medianoche',
            'The Outlaws' => 'Los Forajidos',
            'Veteran' => 'Veterano',
            'Assassination' => 'Asesinato',
            'The Thieves' => 'Los Ladrones',
            'Along with the Gods' => 'Junto con los Dioses',
            'Steel Rain' => 'Lluvia de Acero',
            'The Fortress' => 'La Fortaleza',
            'A Hard Day' => 'Un Día Difícil',
            'New World' => 'Nuevo Mundo',
            'The Gangster, the Cop, the Devil' => 'El Gángster, el Policía, el Diablo',
            'Deliver Us from Evil' => 'Líbranos del Mal',
            'Time to Hunt' => 'Hora de Cazar',
            'Call' => 'Llamada',
            'Alive' => 'Vivo',
            'Peninsula' => 'Península',
            'The Call' => 'La Llamada',
            'Space Sweepers' => 'Barrenderos del Espacio',
            'Seobok' => 'Seobok',
            'Escape from Mogadishu' => 'Escape de Mogadiscio',
            'The Policeman\'s Lineage' => 'El Linaje del Policía',
            'Emergency Declaration' => 'Declaración de Emergencia',
            'Hunt' => 'Cazar',
            'Decision to Leave' => 'Decisión de Partir',
            'The Roundup' => 'La Redada',
        ];

        foreach ($movieTranslations as $original => $spanish) {
            Movie::where(function($query) use ($original) {
                $query->where('title', $original)
                      ->orWhere('original_title', $original);
            })->update([
                'spanish_title' => $spanish
            ]);
        }

        $this->info('🎬 Movie titles updated');
    }

    private function fixGenericContent()
    {
        // Para contenido sin título español, usar el título original
        Series::whereNull('title_es')
            ->orWhere('title_es', '')
            ->update(['title_es' => \DB::raw('COALESCE(title, original_title)')]);

        Movie::whereNull('spanish_title')
            ->orWhere('spanish_title', '')
            ->update(['spanish_title' => \DB::raw('COALESCE(title, original_title)')]);

        // Para overview sin traducir, usar el overview original
        Series::whereNull('overview_es')
            ->orWhere('overview_es', '')
            ->update(['overview_es' => \DB::raw('overview')]);

        Movie::whereNull('spanish_overview')
            ->orWhere('spanish_overview', '')
            ->update(['spanish_overview' => \DB::raw('overview')]);

        $this->info('📝 Generic content fallbacks applied');
    }
}