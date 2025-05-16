<?php

namespace App\Console\Commands;

use App\Models\Title;
use App\Models\ProfessionalReview;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportProfessionalReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reviews:import {--title-id=} {--limit=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import professional reviews for titles from external sources';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $titleId = $this->option('title-id');
        $limit = $this->option('limit');
        
        if ($titleId) {
            $title = Title::find($titleId);
            if (!$title) {
                $this->error("Title with ID {$titleId} not found.");
                return;
            }
            $this->importReviewsForTitle($title);
        } else {
            // Import reviews for titles that don't have enough reviews
            $titles = Title::has('professionalReviews', '<', 2)
                ->limit($limit)
                ->get();
                
            $this->info("Importing reviews for {$titles->count()} titles...");
            
            foreach ($titles as $title) {
                $this->importReviewsForTitle($title);
            }
        }
        
        $this->info('Professional reviews import completed!');
    }
    
    private function importReviewsForTitle(Title $title)
    {
        $this->info("Importing reviews for: {$title->title}");
        
        // Import from TMDb reviews (if available)
        $this->importFromTmdb($title);
        
        // Import simulated reviews from various sources
        $this->importSimulatedReviews($title);
    }
    
    private function importFromTmdb(Title $title)
    {
        if (!$title->tmdb_id || !config('services.tmdb.api_key')) {
            return;
        }
        
        try {
            $response = Http::get("https://api.themoviedb.org/3/{$title->type}/{$title->tmdb_id}/reviews", [
                'api_key' => config('services.tmdb.api_key'),
                'language' => 'es-ES'
            ]);
            
            if ($response->successful()) {
                $results = $response->json()['results'] ?? [];
                
                foreach (array_slice($results, 0, 2) as $review) {
                    ProfessionalReview::updateOrCreate(
                        ['external_id' => $review['id']],
                        [
                            'title_id' => $title->id,
                            'reviewer_name' => $review['author_details']['username'] ?? 'Crítico TMDb',
                            'reviewer_source' => 'TMDb',
                            'content' => $review['content'],
                            'rating' => $review['author_details']['rating'] ?? null,
                            'review_date' => isset($review['created_at']) ? date('Y-m-d', strtotime($review['created_at'])) : now(),
                            'review_url' => $review['url'] ?? null,
                            'language' => 'es',
                            'is_featured' => ($review['author_details']['rating'] ?? 0) >= 8
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error("Error importing TMDb reviews for title {$title->id}: " . $e->getMessage());
        }
    }
    
    private function importSimulatedReviews(Title $title)
    {
        // Simulated review sources
        $reviewSources = [
            [
                'source' => 'CineAsiático',
                'reviewers' => ['Maria González', 'Juan Chen', 'Ana Kimura'],
                'style' => 'professional'
            ],
            [
                'source' => 'Asian Drama Review',
                'reviewers' => ['Lisa Park', 'Michael Lee', 'Sophie Wang'],
                'style' => 'detailed'
            ],
            [
                'source' => 'Doramas & Más',
                'reviewers' => ['Carlos López', 'Patricia Silva', 'Roberto Sánchez'],
                'style' => 'casual'
            ]
        ];
        
        // Review templates based on genre and type
        $templates = [
            'drama' => [
                'positive' => [
                    "{title} destaca por su narrativa emotiva y las actuaciones extraordinarias de su elenco. La dirección es impecable y la cinematografía captura perfectamente la esencia de {country}.",
                    "Una obra maestra del drama {country} que combina perfectamente elementos modernos con tradiciones culturales. {title} es imprescindible para los amantes del género.",
                    "Con una trama cautivadora y personajes profundamente desarrollados, {title} establece un nuevo estándar para las producciones de {country}."
                ],
                'neutral' => [
                    "{title} ofrece momentos memorables aunque sufre de un ritmo irregular en algunos episodios. Los fans del género encontrarán mucho que disfrutar.",
                    "Aunque {title} tiene sus altibajos, las actuaciones sólidas y la producción de calidad la mantienen entretenida hasta el final.",
                    "Una propuesta interesante que, pese a algunos defectos narrativos, logra mantener el interés del espectador."
                ]
            ],
            'romance' => [
                'positive' => [
                    "{title} es una hermosa historia de amor que captura la magia del romance {country}. La química entre los protagonistas es innegable.",
                    "Una joya romántica que combina humor, drama y momentos conmovedores. {title} te robará el corazón desde el primer episodio.",
                    "Con escenas memorables y una banda sonora excepcional, {title} redefine el género romántico en las producciones de {country}."
                ],
                'neutral' => [
                    "{title} sigue las convenciones del género pero lo hace con suficiente encanto para mantener al espectador enganchado.",
                    "Aunque predecible en momentos, {title} compensa con actuaciones carismáticas y momentos genuinamente emotivos.",
                    "Una historia de amor convencional elevada por la química de sus protagonistas y la hermosa cinematografía."
                ]
            ],
            'action' => [
                'positive' => [
                    "{title} ofrece secuencias de acción espectaculares y una trama trepidante que mantiene la tensión hasta el final.",
                    "Una producción de alto calibre que demuestra el dominio de {country} en el género de acción. Las escenas de pelea son coreografiadas magistralmente.",
                    "Con efectos visuales impresionantes y actuaciones intensas, {title} es adrenalina pura de principio a fin."
                ],
                'neutral' => [
                    "{title} cumple con las expectativas del género aunque no innova significativamente. Los fans de la acción quedarán satisfechos.",
                    "Aunque las escenas de acción son competentes, la trama de {title} podría haber sido más desarrollada.",
                    "Una entrada sólida en el género de acción que, pese a sus limitaciones, ofrece entretenimiento constante."
                ]
            ]
        ];
        
        // Get the main genre of the title
        $genre = $title->genres->first();
        $genreKey = 'drama'; // default
        
        if ($genre) {
            if (stripos($genre->name, 'romance') !== false) $genreKey = 'romance';
            elseif (stripos($genre->name, 'action') !== false || stripos($genre->name, 'acción') !== false) $genreKey = 'action';
        }
        
        // Create 2-3 reviews per title
        $reviewCount = rand(2, 3);
        $usedSources = [];
        
        for ($i = 0; $i < $reviewCount; $i++) {
            // Select a random source that hasn't been used yet
            $availableSources = array_filter($reviewSources, function($source) use ($usedSources) {
                return !in_array($source['source'], $usedSources);
            });
            
            if (empty($availableSources)) {
                $availableSources = $reviewSources;
                $usedSources = [];
            }
            
            $source = $availableSources[array_rand($availableSources)];
            $usedSources[] = $source['source'];
            
            // Select sentiment (more positive reviews than neutral)
            $sentiment = rand(1, 10) <= 7 ? 'positive' : 'neutral';
            
            // Select template
            $templateArray = $templates[$genreKey][$sentiment];
            $template = $templateArray[array_rand($templateArray)];
            
            // Replace placeholders
            $content = str_replace(
                ['{title}', '{country}'],
                [$title->title, strtolower($title->country)],
                $template
            );
            
            // Calculate rating based on sentiment
            $rating = $sentiment === 'positive' ? rand(75, 95) / 10 : rand(60, 74) / 10;
            
            // Create the review
            ProfessionalReview::create([
                'title_id' => $title->id,
                'reviewer_name' => $source['reviewers'][array_rand($source['reviewers'])],
                'reviewer_source' => $source['source'],
                'content' => $content,
                'rating' => $rating,
                'review_date' => now()->subDays(rand(1, 365)),
                'review_url' => null,
                'language' => 'es',
                'is_featured' => $rating >= 8.0
            ]);
        }
        
        $this->info("Created {$reviewCount} simulated reviews for {$title->title}");
    }
}