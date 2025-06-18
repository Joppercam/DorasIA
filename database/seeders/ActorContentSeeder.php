<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Person;
use App\Models\ActorContent;
use Carbon\Carbon;

class ActorContentSeeder extends Seeder
{
    public function run()
    {
        // Obtener algunos actores populares
        $actors = Person::whereNotNull('profile_path')
            ->where('popularity', '>', 5)
            ->limit(10)
            ->get();

        $contentTypes = [
            'interview',
            'behind_scenes',
            'biography',
            'news',
            'gallery',
            'video',
            'article',
            'timeline',
            'trivia',
            'social'
        ];

        $sampleContent = [
            'interview' => [
                'titles' => [
                    'Entrevista exclusiva: Sus inicios en la actuación',
                    'Conversando sobre su último proyecto',
                    'Detrás de cámaras: Una charla íntima',
                    'Sus secretos para interpretar personajes',
                    'Reflexiones sobre su carrera artística'
                ],
                'contents' => [
                    'En esta entrevista exclusiva, el actor comparte detalles nunca antes revelados sobre sus inicios en la industria del entretenimiento...',
                    'Una conversación profunda sobre los desafíos y alegrías de interpretar personajes complejos en el mundo del K-Drama...',
                    'Descubre los momentos más emotivos detrás de cámaras y cómo se prepara para cada nuevo rol...',
                    'Técnicas de actuación, inspiraciones y consejos para actores jóvenes que quieren seguir sus pasos...',
                    'Un repaso por los momentos más significativos de su trayectoria artística y planes futuros...'
                ]
            ],
            'behind_scenes' => [
                'titles' => [
                    'Making of: Preparación para el personaje',
                    'Un día en el set de grabación',
                    'Ensayos y preparación física',
                    'Bloopers y momentos divertidos',
                    'El proceso creativo detrás de escenas'
                ],
                'contents' => [
                    'Acompáñanos en el proceso completo de preparación para uno de sus personajes más desafiantes...',
                    'Una mirada íntima a cómo transcurre un día típico durante las grabaciones...',
                    'El arduo trabajo físico y mental que requiere cada interpretación...',
                    'Los momentos más divertidos y espontáneos que no llegaron a pantalla...',
                    'Descubre el trabajo en equipo que hace posible cada producción...'
                ]
            ],
            'news' => [
                'titles' => [
                    'Confirmado para nuevo drama histórico',
                    'Gana premio como Mejor Actor del Año',
                    'Anuncia proyecto benéfico especial',
                    'Próximo estreno en plataforma internacional',
                    'Colaboración especial con director reconocido'
                ],
                'contents' => [
                    'Se confirma oficialmente su participación en una nueva producción que promete ser uno de los dramas más esperados...',
                    'Reconocimiento merecido por su excepcional actuación en el drama que conquistó corazones...',
                    'Inicia una fundación para apoyar a jóvenes artistas y causas sociales importantes...',
                    'Su trabajo trasciende fronteras y llega a audiencias internacionales...',
                    'Una nueva oportunidad de trabajar con uno de los directores más respetados de la industria...'
                ]
            ],
            'trivia' => [
                'titles' => [
                    '10 datos curiosos que no sabías',
                    'Sus hobbies fuera de la actuación',
                    'Anécdotas divertidas del pasado',
                    'Datos sobre su familia y origen',
                    'Curiosidades de sus personajes favoritos'
                ],
                'contents' => [
                    'Descubre facetas desconocidas de la vida personal y profesional de esta estrella...',
                    'Más allá de la actuación, tiene pasiones y talentos que te sorprenderán...',
                    'Historias divertidas de sus primeros años en el mundo del entretenimiento...',
                    'Conoce más sobre sus raíces familiares y las influencias que marcaron su carrera...',
                    'Los personajes que más ha disfruado interpretar y los desafíos que representaron...'
                ]
            ]
        ];

        foreach ($actors as $actor) {
            // Crear entre 3-8 contenidos por actor
            $contentCount = rand(3, 8);
            
            for ($i = 0; $i < $contentCount; $i++) {
                $type = $contentTypes[array_rand($contentTypes)];
                $typeData = $sampleContent[$type] ?? $sampleContent['interview'];
                
                $titleIndex = array_rand($typeData['titles']);
                $contentIndex = array_rand($typeData['contents']);
                
                ActorContent::create([
                    'person_id' => $actor->id,
                    'type' => $type,
                    'title' => $typeData['titles'][$titleIndex] . ' - ' . $actor->display_name,
                    'content' => $typeData['contents'][$contentIndex],
                    'media_url' => $this->getRandomMediaUrl($type),
                    'media_type' => $this->getMediaType($type),
                    'thumbnail_url' => $this->getRandomThumbnail(),
                    'duration' => $type === 'video' ? rand(120, 1800) : null, // 2-30 minutos para videos
                    'is_exclusive' => rand(0, 100) < 70, // 70% exclusivo
                    'is_featured' => rand(0, 100) < 30, // 30% destacado
                    'published_at' => Carbon::now()->subDays(rand(1, 60)), // Últimos 2 meses
                    'source' => 'Dorasia Exclusive',
                    'tags' => $this->getRandomTags($type, $actor->display_name),
                    'view_count' => rand(50, 2000),
                    'like_count' => rand(5, 200),
                    'metadata' => [
                        'quality' => $type === 'video' ? '1080p' : null,
                        'language' => 'ko',
                        'subtitles' => ['es', 'en'],
                        'category' => $this->getContentCategory($type)
                    ]
                ]);
            }
        }
    }

    private function getRandomMediaUrl($type)
    {
        $baseUrls = [
            'video' => 'https://example.com/videos/',
            'image' => 'https://example.com/images/',
            'audio' => 'https://example.com/audio/'
        ];

        $mediaType = $this->getMediaType($type);
        $baseUrl = $baseUrls[$mediaType] ?? $baseUrls['image'];
        
        return $baseUrl . uniqid() . '.' . ($mediaType === 'video' ? 'mp4' : ($mediaType === 'audio' ? 'mp3' : 'jpg'));
    }

    private function getMediaType($type)
    {
        $videoTypes = ['video', 'interview', 'behind_scenes'];
        $imageTypes = ['gallery', 'news', 'biography', 'timeline', 'trivia', 'social'];
        
        if (in_array($type, $videoTypes)) {
            return 'video';
        } elseif (in_array($type, $imageTypes)) {
            return 'image';
        }
        
        return 'image';
    }

    private function getRandomThumbnail()
    {
        // Usando placeholders realistas
        $thumbnails = [
            'https://picsum.photos/400/300?random=1',
            'https://picsum.photos/400/300?random=2',
            'https://picsum.photos/400/300?random=3',
            'https://picsum.photos/400/300?random=4',
            'https://picsum.photos/400/300?random=5',
        ];
        
        return $thumbnails[array_rand($thumbnails)];
    }

    private function getRandomTags($type, $actorName)
    {
        $baseTags = [$actorName, 'K-Drama', 'Actor Coreano'];
        
        $typeTags = [
            'interview' => ['Entrevista', 'Exclusivo', 'Personal'],
            'behind_scenes' => ['BTS', 'Making Of', 'Detrás de Cámaras'],
            'news' => ['Noticias', 'Actualidad', 'Última Hora'],
            'biography' => ['Biografía', 'Historia Personal', 'Vida'],
            'gallery' => ['Fotos', 'Galería', 'Imágenes'],
            'video' => ['Video', 'Multimedia', 'Visual'],
            'trivia' => ['Curiosidades', 'Datos', 'Divertido'],
            'timeline' => ['Cronología', 'Historia', 'Carrera'],
            'social' => ['Redes Sociales', 'Instagram', 'SNS']
        ];
        
        $tags = array_merge($baseTags, $typeTags[$type] ?? []);
        
        // Agregar algunas tags aleatorias
        $randomTags = ['Exclusivo', 'Premium', 'Nuevo', 'Popular', 'Trending'];
        $tags[] = $randomTags[array_rand($randomTags)];
        
        return array_unique($tags);
    }

    private function getContentCategory($type)
    {
        $categories = [
            'interview' => 'Entrevistas',
            'behind_scenes' => 'Making Of',
            'news' => 'Noticias',
            'biography' => 'Biografías',
            'gallery' => 'Galerías',
            'video' => 'Videos',
            'trivia' => 'Entretenimiento',
            'timeline' => 'Historia',
            'social' => 'Redes Sociales'
        ];
        
        return $categories[$type] ?? 'General';
    }
}