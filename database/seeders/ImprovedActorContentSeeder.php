<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Person;
use App\Models\ActorContent;
use Carbon\Carbon;

class ImprovedActorContentSeeder extends Seeder
{
    /**
     * Contenido rico y detallado para actores
     */
    private $richContent = [
        'interview' => [
            [
                'title' => 'Entrevista exclusiva: "Mi transformación para este personaje cambió mi vida"',
                'content' => 'En esta entrevista íntima transcrita, el actor revela cómo su último papel lo llevó a explorar emociones profundas que nunca había experimentado antes. "Durante tres meses viví como mi personaje", nos cuenta mientras reflexiona sobre el proceso de preparación que incluía levantarse a las 4 AM para entrenar artes marciales y estudiar dialectos regionales.

"Creo que cada actor tiene un personaje que lo marca para siempre, y este definitivamente es el mío", explica mientras sus ojos se iluminan al recordar los momentos más intensos del rodaje. El actor también comparte anécdotas divertidas del set, como la vez que improvisó una escena que terminó siendo una de las más memorables de la serie.

Cuando le preguntamos sobre sus próximos proyectos, sonríe misteriosamente: "Solo puedo decir que estoy emocionado por explorar un género completamente diferente. Mis fans se van a sorprender."

**Entrevistador**: ¿Cuál fue el momento más desafiante durante la preparación?

**Actor**: Sin duda, aprender el dialecto regional. Pasé horas con un coach de dialectos y aún tengo grabaciones de práctica en mi teléfono.

**Entrevistador**: ¿Qué consejo le darías a actores jóvenes que quieren seguir tu camino?

**Actor**: Nunca dejen de estudiar. Cada personaje es una universidad nueva. Y sobre todo, manténganse humildes y agradecidos.',
                'tags' => ['Entrevista transcrita', 'Exclusivo', 'Personal', 'Transformación', 'Proceso creativo']
            ],
            [
                'title' => 'De novato a estrella: El viaje de 10 años que cambió el K-Drama',
                'content' => 'Hace exactamente una década, un joven actor de 22 años llegó a Seúl con solo 200,000 won en el bolsillo y un sueño. Hoy, es una de las estrellas más reconocidas del entretenimiento coreano.

"Recuerdo mi primer casting", dice riéndose. "Estaba tan nervioso que me olvidé por completo de mis líneas. El director me pidió que improvisara y terminé cantando una canción de mi infancia. De alguna manera, eso los convenció de que tenía algo especial."

Los primeros años no fueron fáciles. Trabajó como extra en más de 50 producciones, durmió en gosiwons diminutos y vivió de ramen instantáneo. "Había días en que pensaba en rendirme, pero algo dentro de mí sabía que esto era lo que tenía que hacer."

Su breakthrough llegó con un papel secundario en un drama nocturno que nadie esperaba que fuera exitoso. "El guión era increíble, pero el presupuesto era muy limitado. Todos pusimos nuestro corazón en ese proyecto." La serie se convirtió en un fenómeno cultural y su carrera despegó.

"El éxito no cambió quién soy", reflexiona. "Si algo, me hizo más consciente de la responsabilidad que tengo hacia mis fans y hacia la industria que me dio esta oportunidad."

**Pregunta**: ¿Cuál fue tu mayor sacrificio durante esos años difíciles?

**Respuesta**: Perderme momentos importantes con mi familia. Cuando mi abuela cumplió 80 años, yo estaba en un set trabajando como extra. Ese tipo de sacrificios pesan, pero también te dan perspectiva.',
                'tags' => ['Historia personal', 'Carrera', 'Inspiración', 'Superación', 'Éxito', 'Entrevista']
            ]
        ],

        'biography' => [
            [
                'title' => 'La infancia que forjó a una estrella: Creciendo en Busan',
                'content' => 'Nacido en un barrio humilde de Busan, la segunda ciudad más grande de Corea del Sur, su infancia estuvo marcada por la determinación de una madre soltera que trabajaba dos empleos para mantener a la familia a flote.

"Mi madre trabajaba en una fábrica de textiles durante el día y limpiaba oficinas por las noches", recuerda con cariño. "Yo pasaba las tardes en la biblioteca local porque era el único lugar con calefacción gratuita donde podía hacer mis tareas."

Fue en esa biblioteca donde descubrió su pasión por las historias. "Leía todo lo que podía encontrar: novelas, obras de teatro, guiones de películas. La bibliotecaria, la señora Kim, se dio cuenta de mi interés y comenzó a recomendarme libros especiales."

Su primer contacto con la actuación llegó por accidente a los 12 años, cuando su escuela necesitaba un reemplazo de último minuto para una obra escolar. "Tenía miedo escénico terrible, pero algo cambió cuando pisé ese escenario improvisado en el gimnasio. Por primera vez en mi vida, me sentí completamente en casa."

La señora Kim, quien se había convertido en una figura materna para él, lo animó a participar en talleres de teatro juvenil los fines de semana. "Ella usaba su propio dinero para pagarme el transporte", dice con lágrimas en los ojos. "Sin ella, nada de esto habría sido posible."

A los 16 años, ganó un concurso de actuación regional que incluía una beca para estudiar en Seúl. "Recuerdo el día que llegué a la capital con una maleta vieja y una carta de recomendación de la señora Kim. Estaba aterrorizado pero emocionado."',
                'tags' => ['Biografía', 'Infancia', 'Busan', 'Madre soltera', 'Biblioteca', 'Inspiración']
            ]
        ],

        'news' => [
            [
                'title' => 'EXCLUSIVA: Firmado para liderar el drama histórico más caro de la historia de Corea',
                'content' => 'En una decisión que ha sacudido la industria del entretenimiento, el actor ha sido elegido para protagonizar "Reino de los Cielos", una producción épica con un presupuesto de 50 mil millones de won (aproximadamente 40 millones de dólares), convirtiéndolo en el drama histórico más caro jamás producido en Corea del Sur.

La serie, que abarcará 24 episodios en dos temporadas, seguirá la vida del Rey Sejong el Grande y su corte durante el período Joseon. "Este es el papel de mi vida", declaró en una conferencia de prensa exclusiva. "He estado preparándome durante seis meses, estudiando historia coreana clásica y aprendiendo el dialecto cortesano del siglo XV."

El casting incluye a estrellas de primera categoría y un equipo de producción internacional. Las filmaciones comenzarán en primavera en locaciones históricas reales, incluyendo el Palacio Gyeongbokgung y sets construidos específicamente que recrean la Seúl del siglo XV.

"Queremos que esta serie sea nuestro regalo al mundo", explica el productor ejecutivo. "Una manera de mostrar la rica historia y cultura coreana con la más alta calidad de producción."

Las expectativas son enormes. Pre-venta internacional ya ha superado los 30 millones de dólares, con Netflix, Amazon Prime y otras plataformas en guerra por los derechos de distribución global.

"Siento la responsabilidad", admite el actor. "No solo estoy interpretando a un personaje histórico real, sino que estoy representando mi país ante el mundo. Es un honor y un desafío que acepto con humildad."',
                'tags' => ['Exclusiva', 'Drama histórico', 'Presupuesto récord', 'Rey Sejong', 'Internacional', 'Netflix']
            ]
        ],

        'trivia' => [
            [
                'title' => '15 secretos fascinantes que nunca imaginaste',
                'content' => '1. **Políglota secreto**: Habla fluidamente 5 idiomas (coreano, inglés, japonés, mandarín y español) y está aprendiendo italiano para un futuro proyecto internacional.

2. **Chef amateur**: Antes de ser actor, trabajó en la cocina de un restaurante francés en Seúl. Aún cocina para todo el elenco durante las filmaciones largas.

3. **Fobia sorprendente**: A pesar de hacer sus propias acrobacias, tiene pánico a las mariposas desde la infancia cuando una mariposa gigante lo siguió por todo un parque.

4. **Músico oculto**: Compone música en su tiempo libre y ha escrito 3 OSTs para dramas, aunque nunca ha revelado cuáles son.

5. **Rutina matutina única**: Se levanta a las 4:30 AM todos los días para meditar 30 minutos y practicar caligrafía tradicional coreana.

6. **Coleccionista peculiar**: Colecciona lápices vintage de todo el mundo. Su colección incluye más de 500 lápices únicos.

7. **Superstición divertida**: Siempre usa calcetines rojos el primer día de filmación de cualquier proyecto. Los considera su amuleto de la suerte.

8. **Talento oculto**: Puede resolver un cubo de Rubik en menos de 2 minutos. Lo aprendió durante una época de insomnio y ahora lo usa para calmar los nervios.

9. **Activista silencioso**: Dona el 30% de sus ganancias a organizaciones que apoyan la educación de niños de bajos recursos, pero nunca lo ha publicitado.

10. **Memoria fotográfica**: Puede memorizar un guión completo de 60 páginas en menos de 4 horas. Esta habilidad le ha valido la admiración de directores.

11. **Pasión por la arquitectura**: En su tiempo libre estudia arquitectura tradicional coreana y ha diseñado su propia casa siguiendo principios del feng shui.

12. **Entrenamiento secreto**: Practica taekwondo desde los 8 años y tiene cinturón negro 4° dan, aunque rara vez habla de ello públicamente.

13. **Adicto al café**: Bebe exactamente 4 tazas de café americano al día, siempre a la misma temperatura (65°C), y conoce el nombre de todos los baristas de sus cafeterías favoritas.

14. **Escritor en secreto**: Ha escrito un libro de cuentos cortos que planea publicar después de cumplir 40 años. Solo 3 personas han leído el manuscrito.

15. **Tradición familiar**: Cada año, el día de su cumpleaños, vuelve a su ciudad natal para comer en el mismo restaurante local donde celebró su primer cumpleaños con éxito profesional.',
                'tags' => ['Secretos', 'Datos curiosos', 'Vida personal', 'Hobbies', 'Talentos ocultos']
            ]
        ],

        'timeline' => [
            [
                'title' => 'Cronología completa: De estudiante a leyenda del entretenimiento',
                'content' => '**1995 - Nacimiento**: Nace en Busan en una familia de clase trabajadora.

**2001 (6 años) - Primeros pasos**: Participa en su primer espectáculo escolar interpretando a un árbol. Su maestra nota su presencia natural en el escenario.

**2007 (12 años) - Despertar artístico**: Se une al club de teatro de su escuela secundaria. Gana su primer premio de actuación en un festival estudiantil regional.

**2010 (15 años) - Decisión crucial**: Convince a su familia de permitirle audicionar para escuelas de arte en Seúl, a pesar de las dificultades económicas.

**2013 (18 años) - Llegada a Seúl**: Se muda a la capital para estudiar en la Universidad de Artes de Seúl. Vive en un gosiwon de 3m² y trabaja de noche en un café.

**2014 (19 años) - Primer papel**: Aparece como extra en 15 producciones diferentes. Gana 50,000 won por día y ahorra cada centavo.

**2015 (20 años) - Breakthrough menor**: Consigue un papel pequeño pero memorable en un drama nocturno. Los fans comienzan a notarlo en foros online.

**2016 (21 años) - Papel de apoyo**: Interpreta al mejor amigo del protagonista en un drama juvenil exitoso. Su personaje se vuelve tan popular que los escritores expanden su arco narrativo.

**2017 (22 años) - Reconocimiento**: Gana su primer premio como "Actor Revelación" en los KBS Drama Awards. Su discurso de agradecimiento se vuelve viral.

**2018 (23 años) - Primer protagónico**: Lidera su primer drama romántico. La serie alcanza ratings del 15% y establece su reputación como actor principal.

**2019 (24 años) - Expansión internacional**: Su drama se distribuye globalmente en Netflix. Gana fans internacionales y aprende inglés intensivamente.

**2020 (25 años) - Pandemia y crecimiento**: Durante la pandemia, utiliza el tiempo para estudiar actuación método y escritura de guiones. Crea contenido para sus fans desde casa.

**2021 (26 años) - Consagración**: Protagoniza el drama que lo catapulta al estrellato internacional. Gana múltiples premios y es invitado a festivales de cine internacionales.

**2022 (27 años) - Diversificación**: Debuta en cine con una película independiente aclamada por la crítica. También lanza su primera empresa de producción.

**2023 (28 años) - Presente**: Considerado uno de los actores más influyentes de su generación. Sus dramas generan más de 100 millones de dólares en valor económico.

**2024 (29 años) - Futuro**: Próximos proyectos incluyen su primera película de Hollywood y un drama histórico con el presupuesto más alto en la historia coreana.',
                'tags' => ['Cronología', 'Carrera', 'Evolución', 'Hitos', 'Historia profesional']
            ]
        ]
    ];

    public function run()
    {
        // Limpiar contenido existente
        ActorContent::truncate();

        // Obtener actores populares
        $actors = Person::whereNotNull('profile_path')
            ->where('popularity', '>', 5)
            ->limit(15)
            ->get();

        if ($actors->isEmpty()) {
            $this->command->warn('No se encontraron actores para generar contenido');
            return;
        }

        foreach ($actors as $actor) {
            $this->createRichContentForActor($actor);
        }

        $this->command->info('Contenido rico generado para ' . $actors->count() . ' actores');
    }

    private function createRichContentForActor($actor)
    {
        // Crear diferentes tipos de contenido para cada actor - solo contenido de texto disponible
        $contentTypesToCreate = [
            'interview' => rand(2, 3),  // Entrevistas transcritas
            'biography' => 1,           // Biografías
            'news' => rand(2, 3),       // Noticias
            'trivia' => 1,              // Curiosidades
            'timeline' => 1,            // Cronología
            'article' => rand(1, 2),    // Artículos
            'social' => rand(0, 1)      // Redes sociales
        ];

        foreach ($contentTypesToCreate as $type => $count) {
            for ($i = 0; $i < $count; $i++) {
                $this->createSpecificContent($actor, $type, $i);
            }
        }
    }

    private function createSpecificContent($actor, $type, $index)
    {
        // Obtener contenido base para el tipo
        $baseContent = $this->richContent[$type] ?? [];
        
        if (empty($baseContent)) {
            // Fallback para tipos sin contenido específico
            $content = $this->generateGenericContent($type, $actor);
        } else {
            // Usar contenido específico o generar variación
            $contentIndex = $index % count($baseContent);
            $content = $baseContent[$contentIndex];
            
            // Personalizar el contenido para el actor específico
            $content = $this->personalizeContent($content, $actor, $type);
        }

        // Crear el registro
        ActorContent::create([
            'person_id' => $actor->id,
            'type' => $type,
            'title' => $content['title'],
            'content' => $content['content'],
            'media_url' => $this->generateMediaUrl($type),
            'media_type' => $this->getMediaType($type),
            'thumbnail_url' => $this->generateThumbnail($type),
            'duration' => $content['duration'] ?? $this->getDefaultDuration($type),
            'is_exclusive' => rand(0, 100) < 80, // 80% exclusivo
            'is_featured' => rand(0, 100) < 25, // 25% destacado
            'published_at' => $this->getRandomPublishDate(),
            'source' => $this->getContentSource($type),
            'external_url' => $type === 'social' ? $this->generateSocialUrl($actor) : null,
            'tags' => $content['tags'] ?? $this->generateTags($type, $actor),
            'view_count' => $this->getRealisticViewCount($type),
            'like_count' => $this->getRealisticLikeCount(),
            'metadata' => $this->generateMetadata($type, $actor)
        ]);
    }

    private function personalizeContent($content, $actor, $type)
    {
        // Reemplazar placeholders genéricos con información del actor
        $replacements = [
            'el actor' => $actor->display_name,
            'El actor' => $actor->display_name,
            'su último papel' => 'su papel en "' . $this->generateDramaTitle() . '"',
            'la serie' => '"' . $this->generateDramaTitle() . '"',
            'el drama' => '"' . $this->generateDramaTitle() . '"'
        ];

        $personalizedContent = $content;
        
        foreach ($replacements as $search => $replace) {
            $personalizedContent['title'] = str_replace($search, $replace, $personalizedContent['title']);
            $personalizedContent['content'] = str_replace($search, $replace, $personalizedContent['content']);
        }

        // Agregar el nombre del actor al título si no está presente
        if (!str_contains($personalizedContent['title'], $actor->display_name)) {
            $personalizedContent['title'] = $personalizedContent['title'] . ' - ' . $actor->display_name;
        }

        return $personalizedContent;
    }

    private function generateGenericContent($type, $actor)
    {
        $genericTemplates = [
            'article' => [
                'title' => 'Análisis profundo: El método de actuación que revoluciona el K-Drama - ' . $actor->display_name,
                'content' => 'Un análisis exhaustivo de la técnica única de actuación que ha convertido a ' . $actor->display_name . ' en una referencia para toda una generación de actores. Expertos en actuación y directores reconocidos comparten sus perspectivas sobre el impacto y la innovación que aporta a cada interpretación.

La versatilidad actoral de ' . $actor->display_name . ' se evidencia en su capacidad para transformarse completamente entre personajes. "Cada rol requiere una preparación específica", explican críticos especializados. "Su dedicación al método y su comprensión profunda de la psicología de los personajes lo distingue en la industria."

Su enfoque incluye técnicas de inmersión total, estudio de comportamientos específicos y trabajo corporal intensivo. Esta metodología ha influenciado a una nueva generación de actores que buscan alcanzar el mismo nivel de autenticidad en sus interpretaciones.',
                'tags' => ['Análisis', 'Técnica', 'Método', 'Innovación', 'Actuación']
            ],
            'social' => [
                'title' => 'Momento viral: La publicación que conquistó las redes sociales - ' . $actor->display_name,
                'content' => 'Un momento espontáneo compartido en redes sociales se convirtió en fenómeno viral, mostrando la personalidad genuina de ' . $actor->display_name . ' y generando millones de interacciones. 

La publicación, que consistía en una foto casual tomada durante un descanso en el set, reveló un lado más humano y accesible del actor. Los comentarios de los fans destacaron la naturalidad y carisma que transmite incluso en momentos no planeados.

"Es increíble cómo una imagen simple puede generar tanta conexión", comentan expertos en redes sociales. "Demuestra que la autenticidad sigue siendo el valor más apreciado por las audiencias en la era digital."

El impacto se extendió más allá del entretenimiento, inspirando conversaciones sobre la importancia de mantener la humildad y cercanía con los fans, independientemente del nivel de fama alcanzado.',
                'tags' => ['Viral', 'Redes sociales', 'Momento genuino', 'Conexión', 'Autenticidad']
            ]
        ];

        return $genericTemplates[$type] ?? [
            'title' => ucfirst($type) . ' especial - ' . $actor->display_name,
            'content' => 'Contenido exclusivo de ' . $actor->display_name . ' que revela aspectos únicos de su carrera y personalidad. Este material ofrece una perspectiva íntima y detallada sobre los elementos que han definido su trayectoria en la industria del entretenimiento.',
            'tags' => [ucfirst($type), 'Exclusivo', $actor->display_name]
        ];
    }

    private function generateDramaTitle()
    {
        $titles = [
            'Corazones de Hierro',
            'La Promesa del Rey',
            'Amor en Tiempos de Guerra',
            'Secretos del Palacio',
            'El Último Heredero',
            'Melodía de Primavera',
            'Sombras del Destino',
            'El Príncipe Rebelde',
            'Flores en el Viento',
            'La Venganza del Tiempo'
        ];

        return $titles[array_rand($titles)];
    }

    private function getMediaType($type)
    {
        // Para contenido de texto, usamos 'document' o null
        return 'document';
    }

    private function generateMediaUrl($type)
    {
        // No generamos URLs de video - solo contenido de texto
        return null;
    }

    private function generateThumbnail($type)
    {
        // Ya no necesitamos thumbnails - usaremos las fotos de los actores
        return null;
    }

    private function getDefaultDuration($type)
    {
        // Las entrevistas transcritas no tienen duración de video
        return null;
    }

    private function getRandomPublishDate()
    {
        return Carbon::now()->subDays(rand(1, 90)); // Últimos 3 meses
    }

    private function getContentSource($type)
    {
        $sources = [
            'interview' => 'Dorasia Exclusive Interview',
            'news' => 'Dorasia Entertainment News',
            'biography' => 'Dorasia Biography Series',
            'article' => 'Dorasia Editorial',
            'timeline' => 'Dorasia Timeline',
            'trivia' => 'Dorasia Fun Facts',
            'social' => 'Official Social Media'
        ];

        return $sources[$type] ?? 'Dorasia Exclusive';
    }

    private function generateSocialUrl($actor)
    {
        $platforms = ['instagram', 'twitter', 'weibo'];
        $platform = $platforms[array_rand($platforms)];
        return 'https://' . $platform . '.com/' . str_replace(' ', '', $actor->display_name);
    }

    private function generateTags($type, $actor)
    {
        $baseTags = [$actor->display_name, 'K-Drama', 'Dorasia Exclusive'];
        
        $typeTags = [
            'interview' => ['Entrevista transcrita', 'Exclusivo', 'Personal', 'Revelaciones'],
            'biography' => ['Biografía', 'Historia personal', 'Vida', 'Trayectoria'],
            'news' => ['Noticias', 'Actualidad', 'Breaking News', 'Entretenimiento'],
            'article' => ['Artículo', 'Análisis', 'Editorial', 'Opinión'],
            'timeline' => ['Cronología', 'Historia', 'Carrera', 'Evolución'],
            'trivia' => ['Curiosidades', 'Datos divertidos', 'Secretos', 'Fun Facts'],
            'social' => ['Redes sociales', 'Social Media', 'Viral', 'Interacción']
        ];

        return array_merge($baseTags, $typeTags[$type] ?? []);
    }

    private function getRealisticViewCount($type)
    {
        // Diferentes tipos de contenido tienen diferentes niveles de popularidad
        $ranges = [
            'interview' => [1000, 5000],
            'news' => [2000, 8000],
            'trivia' => [1500, 6000],
            'article' => [800, 3000],
            'biography' => [1200, 4000],
            'timeline' => [900, 3500],
            'social' => [2500, 10000],
            'default' => [300, 1500]
        ];

        $range = $ranges[$type] ?? $ranges['default'];
        return rand($range[0], $range[1]);
    }

    private function getRealisticLikeCount()
    {
        return rand(50, 500); // Proporción realista de likes vs views
    }

    private function generateMetadata($type, $actor)
    {
        $baseMetadata = [
            'language' => 'ko',
            'subtitles' => ['es', 'en'],
            'created_by' => 'Dorasia Content Team',
            'actor_id' => $actor->id,
            'content_rating' => 'All Ages'
        ];

        $typeSpecific = [
            'interview' => [
                'interviewer' => 'Dorasia Editorial Team',
                'location' => 'Dorasia Studios, Seoul',
                'format' => 'transcribed'
            ],
            'article' => [
                'author' => 'Dorasia Editorial Team',
                'word_count' => rand(800, 2000)
            ],
            'news' => [
                'reporter' => 'Dorasia Entertainment News',
                'source_verified' => true
            ]
        ];

        return array_merge($baseMetadata, $typeSpecific[$type] ?? []);
    }
}