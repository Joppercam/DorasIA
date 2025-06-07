<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Series;

class FixSpanishTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dorasia:fix-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Spanish translations for series titles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Corrigiendo traducciones al español...');

        $corrections = [
            // Korean to Spanish translations for popular K-dramas
            '하늘의 인연' => 'Lazo celestial',
            '식스센스' => 'Sexto sentido',
            '런닝맨' => 'Running Man',
            '엄마친구아들' => 'El hijo del amigo de mamá',
            '2월 29일' => '29 de febrero',
            '도깨비' => 'Duende',
            '태양의 후예' => 'Descendientes del sol',
            '괜찮아 사랑이야' => 'Está bien, es amor',
            '시크릿 가든' => 'Jardín secreto',
            '별에서 온 그대' => 'Mi amor de las estrellas',
            '응답하라 1988' => 'Responde 1988',
            '김비서가 왜 그럴까' => '¿Qué pasa con la secretaria Kim?',
            '호텔 델 루나' => 'Hotel del Luna',
            '사랑의 불시착' => 'Aterrizaje de emergencia en tu corazón',
            '킹덤' => 'Kingdom',
            '이태원 클라쓰' => 'Itaewon Class',
            
            // English to Spanish common corrections
            'The Smile Has Left Your Eyes' => 'La sonrisa ha dejado tus ojos',
            'Tale of the Nine Tailed 1938' => 'La leyenda del zorro de nueve colas: 1938',
            'Monstar: Estrella Monstruosa' => 'Monstar',
            '"Doctores: Entre el amor y la medicina"' => 'Doctores',
            'El Reino de King the Land' => 'El reino de la tierra del rey',
            'Duende: El Guardián Solitario' => 'El duende guardián solitario',
            'Haechi: El Guardián del Trono' => 'Haechi: El guardián del trono',
            'El Penthouse: Guerra en el Palacio' => 'El ático: Guerra en la vida',
            'Abogado Shin: Divorcios y Desengaños' => 'Abogado Shin',
        ];

        // Apply specific corrections
        foreach ($corrections as $oldTitle => $newTitle) {
            $updated = Series::where('title_es', $oldTitle)->update(['title_es' => $newTitle]);
            if ($updated > 0) {
                $this->line("✓ '$oldTitle' → '$newTitle'");
            }
        }

        // Remove English articles and common words
        $series = Series::where('display_title', 'LIKE', '%The %')
            ->orWhere('display_title', 'LIKE', '%: The %')
            ->orWhere('display_title', 'LIKE', '%of the %')
            ->get();

        $count = 0;
        foreach ($series as $serie) {
            $originalTitle = $serie->display_title;
            $cleanTitle = $originalTitle;
            
            // Clean up patterns
            $cleanTitle = preg_replace('/^The /', '', $cleanTitle);
            $cleanTitle = preg_replace('/: The /', ': ', $cleanTitle);
            $cleanTitle = str_replace(' of the ', ' de la ', $cleanTitle);
            $cleanTitle = str_replace(' of The ', ' de la ', $cleanTitle);
            $cleanTitle = str_replace(' and the ', ' y la ', $cleanTitle);
            $cleanTitle = str_replace(' and The ', ' y la ', $cleanTitle);
            $cleanTitle = str_replace(' The ', ' la ', $cleanTitle);
            
            if ($cleanTitle !== $originalTitle) {
                $serie->update(['display_title' => $cleanTitle]);
                $this->line("✓ '$originalTitle' → '$cleanTitle'");
                $count++;
            }
        }

        $this->info("✅ Se corrigieron $count traducciones.");
        
        // Show some examples of fixed titles
        $this->info("\n📋 Ejemplos de títulos corregidos:");
        Series::whereNotNull('display_title')
            ->where('display_title', '!=', '')
            ->select('title', 'display_title')
            ->take(5)
            ->get()
            ->each(function($s) {
                $this->line("• {$s->display_title}");
            });
    }
}
