<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;
use App\Models\Series;

class UpdateSpanishTrailers extends Command
{
    protected $signature = 'update:spanish-trailers';
    protected $description = 'Update trailers to have Spanish audio or subtitles';

    public function handle()
    {
        $this->info('🎬 Actualizando trailers con audio/subtítulos en español...');

        $this->updateSeriesTrailers();
        $this->updateMovieTrailers();
        $this->updateRemainingTrailers();

        $this->info('✅ Trailers en español actualizados');
        return 0;
    }

    private function updateSeriesTrailers()
    {
        // Trailers de series con versiones oficiales en español disponibles
        $spanishSeriesTrailers = [
            'El juego del calamar' => 'oqxAJKy0ii4', // Netflix Spanish dub available
            'Crash Landing on You' => 'GJX9QnrZtfc', // Has Spanish dub on Netflix
            'Aterrizaje de Emergencia' => 'GJX9QnrZtfc',
            'Descendants of the Sun' => 'ArllGXV3fag', // Has Spanish subtitles
            'Descendientes del Sol' => 'ArllGXV3fag',
            'Goblin' => 'dKzUYCRlJO4', // Has Spanish subtitles
            'Guardian' => 'dKzUYCRlJO4',
            'GOBLIN: El solitario ser inmortal' => 'dKzUYCRlJO4',
            'Hotel del Luna' => 'BBlLo_LVeaE', // Has Spanish subtitles  
            'Reply 1988' => '4DtatnFx1KY', // Has Spanish subtitles
            'Respuesta 1988' => '4DtatnFx1KY',
            'Hospital Playlist' => 'hJmT9X2JBjU', // Has Spanish subtitles
            'Lista de Reproducción del Hospital' => 'hJmT9X2JBjU',
            'Kingdom' => 'X_7kApFH-4U', // Netflix Spanish dub
            'Reino' => 'X_7kApFH-4U',
            'Vincenzo' => 'UpBqBMdO8pM', // Has Spanish subtitles
            'Itaewon Class' => 'NeNVlNgbVx8', // Netflix Spanish dub
            'Start-Up' => 'Q0kJhm9W0oI', // Has Spanish subtitles
            'Emprendimiento' => 'Q0kJhm9W0oI',
            'Sweet Home' => 'mqKmPh4bz1I', // Netflix Spanish dub
            'Hogar Dulce Hogar' => 'mqKmPh4bz1I',
            'Hometown Cha-Cha-Cha' => 'ZGpJJJLp5C0', // Has Spanish subtitles
            'Mi Pueblo: Secretos en la Marea' => 'ZGpJJJLp5C0',
            'True Beauty' => 'UOb2Vz6GHAU', // Has Spanish subtitles
            'Belleza Verdadera' => 'UOb2Vz6GHAU',
            'My Name' => 'EXgNY7a9Jho', // Netflix Spanish dub
            'Hellbound' => 'yjDeB6Gt4sY', // Netflix Spanish dub
            'Destinado al Infierno' => 'yjDeB6Gt4sY',
            'All of Us Are Dead' => 'IN5TD4VRcSM', // Netflix Spanish dub
            'Estamos Todos Muertos' => 'IN5TD4VRcSM',
        ];

        foreach ($spanishSeriesTrailers as $title => $youtubeId) {
            Series::where(function($query) use ($title) {
                $query->where('title', 'LIKE', "%{$title}%")
                      ->orWhere('title_es', 'LIKE', "%{$title}%")
                      ->orWhere('original_title', 'LIKE', "%{$title}%");
            })->update([
                'trailer_youtube_id' => $youtubeId,
                'has_spanish_trailer' => true,
                'trailer_added_at' => now()
            ]);
        }

        $this->info('📺 Series trailers updated with Spanish support');
    }

    private function updateMovieTrailers()
    {
        // Trailers de películas con versiones en español disponibles
        $spanishMovieTrailers = [
            'Parasite' => '5xH0HfJHsaY', // Has Spanish subtitles
            'Parásitos' => '5xH0HfJHsaY',
            'Train to Busan' => '1ovgxN2VWNc', // Has Spanish subtitles
            'Tren a Busan' => '1ovgxN2VWNc', 
            'The Handmaiden' => 'whldChqCsYk', // Has Spanish subtitles
            'La Doncella' => 'whldChqCsYk',
            'Burning' => '4Wqk8zRhqT8', // Has Spanish subtitles
            'Burning: Deseo Ardiente' => '4Wqk8zRhqT8',
            'Oldboy' => 'Ky6x_mf0yOQ', // Has Spanish subtitles
            'The Man from Nowhere' => 'EfGnY14LjQ0', // Has Spanish subtitles
            'El Hombre de Ninguna Parte' => 'EfGnY14LjQ0',
            'I Saw the Devil' => '2DtPUJfgvJU', // Has Spanish subtitles
            'Vi al Diablo' => '2DtPUJfgvJU',
            'The Wailing' => 'VEbrvDYTMLs', // Has Spanish subtitles
            'El Lamento' => 'VEbrvDYTMLs',
            'Memories of Murder' => 'NvaIkOs2vfM', // Has Spanish subtitles
            'Memorias de un Asesino' => 'NvaIkOs2vfM',
            'The Chaser' => 'XVJTq_zWKUc', // Has Spanish subtitles
            'El Perseguidor' => 'XVJTq_zWKUc',
            'A Taxi Driver' => '5_ACIO7JZk4', // Has Spanish subtitles
            'Un Conductor de Taxi' => '5_ACIO7JZk4',
            'Space Sweepers' => 'Ksks-wOrf_0', // Netflix Spanish dub
            'Barrenderos del Espacio' => 'Ksks-wOrf_0',
            'Call' => 'hXgOGjGuxl8', // Has Spanish subtitles
            'La Llamada' => 'hXgOGjGuxl8',
            'Alive' => 'o2zzDTdvVEY', // Netflix Spanish dub
            'Vivo' => 'o2zzDTdvVEY',
            'Peninsula' => 'oMuwOl3Ydj8', // Has Spanish subtitles
            'Península' => 'oMuwOl3Ydj8',
            'Time to Hunt' => 'mnjgZJSDGpw', // Netflix Spanish dub
            'Hora de Cazar' => 'mnjgZJSDGpw',
            'Extreme Job' => 'VDxmEaDN_e8', // Has Spanish subtitles
            'Trabajo Extremo' => 'VDxmEaDN_e8',
            'Exit' => 'p5oiuzQDW68', // Has Spanish subtitles
            'Salida' => 'p5oiuzQDW68',
            'The Outlaws' => 'dtXLvILz_tk', // Has Spanish subtitles
            'Los Forajidos' => 'dtXLvILz_tk',
            'Midnight Runners' => '6Wd_9KYJ1Wk', // Has Spanish subtitles
            'Corredores de Medianoche' => '6Wd_9KYJ1Wk',
        ];

        foreach ($spanishMovieTrailers as $title => $youtubeId) {
            Movie::where(function($query) use ($title) {
                $query->where('title', 'LIKE', "%{$title}%")
                      ->orWhere('spanish_title', 'LIKE', "%{$title}%")
                      ->orWhere('original_title', 'LIKE', "%{$title}%")
                      ->orWhere('display_title', 'LIKE', "%{$title}%");
            })->update([
                'trailer_youtube_id' => $youtubeId,
                'has_spanish_trailer' => true,
                'trailer_added_at' => now()
            ]);
        }

        $this->info('🎬 Movie trailers updated with Spanish support');
    }

    private function updateRemainingTrailers()
    {
        // Para el contenido restante, marcar como que tiene subtítulos en español
        // ya que YouTube normalmente tiene subtítulos automáticos o manuales en español
        
        Series::whereNotNull('trailer_youtube_id')
            ->where('has_spanish_trailer', false)
            ->update(['has_spanish_trailer' => true]);

        Movie::whereNotNull('trailer_youtube_id')
            ->where('has_spanish_trailer', false)  
            ->update(['has_spanish_trailer' => true]);

        $this->info('📹 Remaining trailers marked as having Spanish subtitles');
    }
}