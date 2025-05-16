<?php

namespace App\Console\Commands;

use App\Models\ProfessionalReview;
use App\Models\Title;
use Illuminate\Console\Command;

class UpdateReviewContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reviews:update-content {--title-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update review content to be more specific for each title';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $titleId = $this->option('title-id');
        
        if ($titleId) {
            $this->updateReviewsForTitle($titleId);
        } else {
            $reviews = ProfessionalReview::with('title.genres')->get();
            
            $bar = $this->output->createProgressBar($reviews->count());
            $bar->start();
            
            foreach ($reviews as $review) {
                $this->updateReview($review);
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
        }
        
        $this->info('Review content update completed!');
    }
    
    private function updateReviewsForTitle($titleId)
    {
        $reviews = ProfessionalReview::where('title_id', $titleId)->with('title.genres')->get();
        
        foreach ($reviews as $review) {
            $this->updateReview($review);
        }
        
        $this->info("Updated {$reviews->count()} reviews for title ID {$titleId}");
    }
    
    private function updateReview(ProfessionalReview $review)
    {
        $title = $review->title;
        $genres = $title->genres->pluck('name')->toArray();
        
        // Determine content based on rating and genre
        $templates = $this->getTemplates($title, $genres);
        
        // Select template based on rating
        if ($review->rating >= 8.0) {
            $template = $templates['excellent'][array_rand($templates['excellent'])];
        } elseif ($review->rating >= 7.0) {
            $template = $templates['good'][array_rand($templates['good'])];
        } elseif ($review->rating >= 6.0) {
            $template = $templates['average'][array_rand($templates['average'])];
        } else {
            $template = $templates['poor'][array_rand($templates['poor'])];
        }
        
        // Replace placeholders
        $content = str_replace(
            ['{title}', '{country}', '{year}', '{type}'],
            [
                $title->title,
                strtolower($title->country ?: 'asiático'),
                $title->release_year ?: date('Y'),
                $title->type === 'movie' ? 'película' : 'serie'
            ],
            $template
        );
        
        $review->update(['content' => $content]);
    }
    
    private function getTemplates(Title $title, array $genres)
    {
        $isRomance = in_array('Romance', $genres) || in_array('Romántico', $genres);
        $isDrama = in_array('Drama', $genres);
        $isAction = in_array('Acción', $genres) || in_array('Action', $genres);
        $isComedy = in_array('Comedia', $genres) || in_array('Comedy', $genres);
        $isThriller = in_array('Thriller', $genres) || in_array('Suspense', $genres);
        
        $templates = [
            'excellent' => [],
            'good' => [],
            'average' => [],
            'poor' => []
        ];
        
        // Romance templates
        if ($isRomance) {
            $templates['excellent'] = array_merge($templates['excellent'], [
                "{title} es una obra maestra del romance {country} que redefine el género. La química entre los protagonistas es palpable y la dirección es sublime. Una experiencia cinematográfica inolvidable que tocará las fibras más sensibles del corazón.",
                "Extraordinaria historia de amor que trasciende las convenciones del género. {title} combina actuaciones memorables con una narrativa profundamente emotiva. Imprescindible para los amantes del cine romántico de calidad.",
                "Con una sensibilidad exquisita, {title} narra una historia de amor que perdurará en la memoria. La cinematografía es hermosa y la banda sonora complementa perfectamente cada escena emotiva."
            ]);
            
            $templates['good'] = array_merge($templates['good'], [
                "{title} ofrece una historia romántica sólida con momentos genuinamente conmovedores. Aunque sigue algunas fórmulas del género, lo hace con suficiente estilo y carisma para destacar.",
                "Una propuesta romántica bien ejecutada que cumple con las expectativas. {title} presenta personajes entrañables y situaciones que resonarán con el público aficionado al género.",
                "Romance bien desarrollado con actuaciones convincentes. {title} logra equilibrar drama y romance de manera efectiva, aunque algunos giros narrativos resultan predecibles."
            ]);
        }
        
        // Drama templates
        if ($isDrama) {
            $templates['excellent'] = array_merge($templates['excellent'], [
                "{title} es un tour de force dramático que explora la condición humana con profundidad y matices. Las actuaciones son extraordinarias y la dirección magistral. Una joya del cine {country} contemporáneo.",
                "Drama profundo y conmovedor que aborda temas universales con una perspectiva única. {title} destaca por su narrativa compleja y personajes tridimensionales que evolucionan de manera orgánica.",
                "Excepcional drama que combina una historia poderosa con actuaciones memorables. {title} es cine en su forma más pura, capaz de emocionar y hacer reflexionar a partes iguales."
            ]);
            
            $templates['good'] = array_merge($templates['good'], [
                "{title} es un drama sólido que explora temas relevantes con sensibilidad. Aunque el ritmo puede ser irregular en momentos, las actuaciones mantienen el interés del espectador.",
                "Drama bien construido con momentos de gran intensidad emocional. {title} beneficia de un elenco talentoso y una dirección competente, aunque la narrativa puede sentirse algo convencional.",
                "Propuesta dramática interesante que, pese a algunos altibajos narrativos, logra transmitir su mensaje central. Las actuaciones son el punto fuerte de esta producción."
            ]);
        }
        
        // Action templates
        if ($isAction) {
            $templates['excellent'] = array_merge($templates['excellent'], [
                "{title} redefine el género de acción con secuencias espectaculares y una narrativa trepidante. La coreografía de las escenas de lucha es impecable y los efectos visuales de primer nivel.",
                "Acción pura y adrenalina constante. {title} combina escenas de acción magistralmente ejecutadas con una historia que mantiene la tensión hasta el último minuto.",
                "Una experiencia cinematográfica explosiva que establece nuevos estándares para el cine de acción {country}. Las secuencias de acción son innovadoras y visualmente impresionantes."
            ]);
            
            $templates['good'] = array_merge($templates['good'], [
                "{title} ofrece entretenimiento sólido con escenas de acción bien coreografiadas. Aunque la trama es algo predecible, la ejecución técnica compensa cualquier deficiencia narrativa.",
                "Película de acción competente que cumple con las expectativas del género. Las secuencias de combate están bien realizadas, aunque la historia podría haber sido más original.",
                "Acción entretenida con momentos destacados. {title} no reinventa el género pero ofrece suficiente espectáculo visual para mantener al público enganchado."
            ]);
        }
        
        // General templates for mixed genres
        $templates['excellent'] = array_merge($templates['excellent'], [
            "{title} es una obra cinematográfica excepcional que combina elementos de {type} de manera magistral. La dirección es impecable y las actuaciones memorables. Una joya del cine {country} que merece reconocimiento internacional.",
            "Extraordinaria producción que eleva los estándares del {type} {country}. {title} ofrece una experiencia visual y narrativa inolvidable, con personajes complejos y una trama cautivadora.",
            "Brillante ejemplo de cómo el cine {country} puede competir con las mejores producciones internacionales. {title} es una obra maestra en todos los aspectos técnicos y narrativos."
        ]);
        
        $templates['good'] = array_merge($templates['good'], [
            "{title} es una {type} bien realizada que cumple con las expectativas. Aunque no innova significativamente, ofrece entretenimiento de calidad con actuaciones sólidas.",
            "Producción competente que demuestra el buen momento del cine {country}. {title} presenta una historia interesante con momentos destacados, aunque el ritmo puede ser irregular.",
            "Propuesta sólida que combina elementos tradicionales con toques modernos. {title} es una {type} recomendable para los aficionados al cine asiático."
        ]);
        
        $templates['average'] = [
            "{title} es una {type} que cumple sin destacar especialmente. Tiene momentos interesantes pero también evidentes fallos de ritmo y desarrollo narrativo.",
            "Propuesta irregular que alterna momentos brillantes con otros más flojos. {title} puede resultar entretenida para fans del género, aunque no será recordada como una obra destacada.",
            "Producción correcta pero olvidable. {title} tiene elementos positivos pero no logra diferenciarse significativamente de otras propuestas similares del cine {country}.",
            "{title} es una {type} promedio que no termina de explotar su potencial. Las actuaciones son desiguales y la dirección poco inspirada."
        ];
        
        $templates['poor'] = [
            "Lamentablemente, {title} no cumple con las expectativas mínimas del género. Los problemas de guion y dirección lastran una premisa que podría haber sido interesante.",
            "Decepcionante {type} que falla en aspectos fundamentales. {title} sufre de un ritmo errático, actuaciones poco convincentes y una narrativa confusa.",
            "{title} es un ejemplo de oportunidad perdida. A pesar de contar con elementos potencialmente interesantes, la ejecución deja mucho que desear.",
            "Producción que no logra conectar con el espectador. {title} presenta múltiples problemas técnicos y narrativos que hacen difícil recomendar esta {type}."
        ];
        
        return $templates;
    }
}