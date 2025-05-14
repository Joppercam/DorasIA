<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            [
                'name' => 'Romance',
                'slug' => 'romance',
                'description' => 'Historias centradas en relaciones románticas y desarrollo de parejas.',
                'tmdb_id' => 10749,
            ],
            [
                'name' => 'Comedia',
                'slug' => 'comedia',
                'description' => 'Series humorísticas diseñadas para hacer reír al espectador.',
                'tmdb_id' => 35,
            ],
            [
                'name' => 'Drama',
                'slug' => 'drama',
                'description' => 'Historias con tono serio y centradas en el desarrollo de personajes y emociones.',
                'tmdb_id' => 18,
            ],
            [
                'name' => 'Histórico',
                'slug' => 'historico',
                'description' => 'Series ambientadas en períodos históricos, generalmente con elementos culturales tradicionales.',
                'tmdb_id' => 36,
            ],
            [
                'name' => 'Fantasía',
                'slug' => 'fantasia',
                'description' => 'Series con elementos sobrenaturales, mágicos o mundos imaginarios.',
                'tmdb_id' => 14,
            ],
            [
                'name' => 'Acción',
                'slug' => 'accion',
                'description' => 'Series con escenas de acción, peleas y secuencias de ritmo intenso.',
                'tmdb_id' => 28,
            ],
            [
                'name' => 'Thriller',
                'slug' => 'thriller',
                'description' => 'Series de suspenso que mantienen al espectador en constante tensión.',
                'tmdb_id' => 53,
            ],
            [
                'name' => 'Misterio',
                'slug' => 'misterio',
                'description' => 'Series centradas en resolver enigmas, crímenes o situaciones inexplicables.',
                'tmdb_id' => 9648,
            ],
            [
                'name' => 'Médico',
                'slug' => 'medico',
                'description' => 'Series ambientadas en hospitales y centradas en profesionales de la salud.',
                'tmdb_id' => 10770,
            ],
            [
                'name' => 'Escolar',
                'slug' => 'escolar',
                'description' => 'Series centradas en la vida de estudiantes, generalmente en secundaria o universidad.',
                'tmdb_id' => 10770,
            ],
            [
                'name' => 'Familiar',
                'slug' => 'familiar',
                'description' => 'Contenido adecuado para todos los públicos y que puede ser disfrutado en familia.',
                'tmdb_id' => 10751,
            ],
            [
                'name' => 'Sobrenatural',
                'slug' => 'sobrenatural',
                'description' => 'Series con elementos paranormales como fantasmas, poderes o fenómenos inexplicables.',
                'tmdb_id' => 10765,
            ],
            [
                'name' => 'Crimen',
                'slug' => 'crimen',
                'description' => 'Series centradas en actividades criminales, detectives o investigaciones policiales.',
                'tmdb_id' => 80,
            ],
            [
                'name' => 'Vida cotidiana',
                'slug' => 'vida-cotidiana',
                'description' => 'Series que retratan la vida diaria y las relaciones humanas en contextos realistas.',
                'tmdb_id' => 10764,
            ],
            [
                'name' => 'Melodrama',
                'slug' => 'melodrama',
                'description' => 'Dramas con alto contenido emocional, a menudo con situaciones trágicas o intensas.',
                'tmdb_id' => 18,
            ],
            [
                'name' => 'Coming-of-age',
                'slug' => 'coming-of-age',
                'description' => 'Historias sobre el crecimiento y maduración de los personajes jóvenes.',
                'tmdb_id' => 10751,
            ],
        ];

        foreach ($genres as $genreData) {
            Genre::updateOrCreate(
                ['slug' => $genreData['slug']],
                $genreData
            );
        }

        $this->command->info('Géneros creados correctamente.');
    }
}