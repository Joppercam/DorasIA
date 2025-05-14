<?php

namespace Database\Seeders;

use App\Models\Title;
use Illuminate\Database\Seeder;

class UpdateTrailersSeeder extends Seeder
{
    /**
     * Añadir trailers a los títulos más populares.
     */
    public function run(): void
    {
        // Lista de trailers conocidos para algunos títulos populares
        $trailers = [
            'Parásitos' => 'https://www.youtube.com/watch?v=5xH0HfJHsaY',
            'Old Boy' => 'https://www.youtube.com/watch?v=2HkjrJ6IK5W',
            'La doncella' => 'https://www.youtube.com/watch?v=whldChqCsYk',
            'Train to Busan' => 'https://www.youtube.com/watch?v=PyrekxsBvNI',
            'El juego del calamar' => 'https://www.youtube.com/watch?v=oqxAJKy0ii4',
            'Memories of Murder' => 'https://www.youtube.com/watch?v=0n_HQwQU8ls',
            'Rompenieves' => 'https://www.youtube.com/watch?v=nX5PwfuFBJU',
            'Exhuma' => 'https://www.youtube.com/watch?v=uByYPksB_EM',
            'Running Man' => 'https://www.youtube.com/watch?v=L37TP_hyqeM',
            'Naruto' => 'https://www.youtube.com/watch?v=zp6xM6Aezmg',
            'One Piece' => 'https://www.youtube.com/watch?v=S8_YwFLCh4U',
            'Hunter x Hunter' => 'https://www.youtube.com/watch?v=d6kBeJjTGnY',
            'Dragon Ball Z' => 'https://www.youtube.com/watch?v=sxufB6DxXk0',
            'Alienoid' => 'https://www.youtube.com/watch?v=KhngXoV9FaQ',
            'Escape' => 'https://www.youtube.com/watch?v=N9qR-rndP2g'
        ];
        
        $updatedCount = 0;
        
        foreach ($trailers as $title => $trailerUrl) {
            // Buscar por título parcial
            $matches = Title::where('title', 'like', "%{$title}%")->get();
            
            foreach ($matches as $match) {
                $match->trailer_url = $trailerUrl;
                $match->save();
                $updatedCount++;
                $this->command->info("Trailer añadido para: {$match->title}");
            }
        }
        
        // Añadir trailers genéricos para títulos populares que no tienen trailer
        $genericTrailers = [
            'k-drama' => [
                'https://www.youtube.com/watch?v=pJrxBc9I3yA', // Trailer de "Goblin" 
                'https://www.youtube.com/watch?v=ZRoJFQmBH3M', // Trailer de "Vincenzo"
                'https://www.youtube.com/watch?v=8aV7eY9Pfl8', // Trailer de "Sweet Home"
                'https://www.youtube.com/watch?v=GXFFvUxFKNs', // Trailer de "Crash Landing on You"
                'https://www.youtube.com/watch?v=FkXnhtXSWqY'  // Trailer de "My Name"
            ],
            'j-drama' => [
                'https://www.youtube.com/watch?v=xv1_mG79Z5I', // Trailer de "Alice in Borderland"
                'https://www.youtube.com/watch?v=4nqIE2zNxNI', // Trailer de "Death Note"
                'https://www.youtube.com/watch?v=HYUeiGo0E5M', // Trailer de "Terrace House"
                'https://www.youtube.com/watch?v=c9XDQHHcLUs'  // Trailer de "The Naked Director"
            ],
            'c-drama' => [
                'https://www.youtube.com/watch?v=5u55UTPf4A4', // Trailer de "The Untamed"
                'https://www.youtube.com/watch?v=c9KmtF6UoOQ', // Trailer de "Nirvana in Fire"
                'https://www.youtube.com/watch?v=xOQeXnhvQE8'  // Trailer de "Eternal Love"
            ],
            'movie' => [
                'https://www.youtube.com/watch?v=VuDeBAYhKO4', // Trailer de "The Wailing"
                'https://www.youtube.com/watch?v=jQ-9n1Z0YUA', // Trailer de "The Handmaiden"
                'https://www.youtube.com/watch?v=Ap0WUCl-ORY'  // Trailer de "A Tale of Two Sisters"
            ]
        ];
        
        // Añadir trailers a títulos populares sin trailers, por categoría
        foreach ($genericTrailers as $type => $trailersList) {
            $query = Title::whereNull('trailer_url')
                ->orWhere('trailer_url', '');
                
            if ($type === 'movie') {
                $query->where('type', 'movie');
            } else {
                $query->whereHas('category', function($q) use ($type) {
                    $q->where('slug', $type);
                });
            }
            
            $titles = $query->where('featured', true)
                ->orWhere('vote_average', '>=', 7.0)
                ->take(count($trailersList))
                ->get();
                
            foreach ($titles as $index => $title) {
                if (isset($trailersList[$index])) {
                    $title->trailer_url = $trailersList[$index];
                    $title->save();
                    $updatedCount++;
                    $this->command->info("Trailer genérico añadido para: {$title->title}");
                }
            }
        }
        
        $this->command->info("Se han actualizado {$updatedCount} títulos con trailers.");
    }
}