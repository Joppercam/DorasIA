<?php

namespace App\Console\Commands;

use App\Models\Title;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TranslateImportedTitles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dorasia:translate-titles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate imported titles to Spanish and make other fixes';

    /**
     * Map of common title translations from Asian languages to Spanish
     */
    protected $titleTranslations = [
        // Títulos en español para dramas coreanos populares
        '논스톱' => 'No Stop',
        '보고 또 보고' => 'Mirar y mirar otra vez',
        '인어아가씨' => 'La Sirenita',
        '난 네게 반했어' => 'Me gustas',
        '더 글로리' => 'La Gloria',
        '구미호뎐' => 'Cuento de la Zorra de Nueve Colas',
        '이상한 변호사 우영우' => 'Woo, una abogada extraordinaria',
        '사랑의 불시착' => 'Aterrizaje de emergencia en tu corazón',
        '지금 우리 학교는' => 'Estamos muertos',
        '오징어 게임' => 'El juego del calamar',
        '경이로운 소문' => 'Rumores increíbles',
        '우리들의 블루스' => 'Nuestros blues',
        '슬기로운 의사생활' => 'Hospital Playlist',
        
        // Títulos en español para dramas japoneses populares
        '藍より青く' => 'Más azul que el índigo',
        '君の名は' => 'Tu nombre',
        '鳩子の海' => 'El mar de Hatoko',
        'あしたの風' => 'El viento de mañana',
        'おしん' => 'Oshin', 
        'たまゆら' => 'Tamayura',
        'ほんまもん' => 'Honmamon',
        'おはなはん' => 'Ohanahan',
        '走らんか！' => '¡Corre!',
        'なっちゃんの写真館' => 'El estudio fotográfico de Natchan',
        'かりん' => 'Karin',
        '本日も晴天なり' => 'Hoy también está despejado',
        'チョッちゃん' => 'Chocchan',
        'すずらん' => 'Lirio de los valles',
        '下町ロケット' => 'Downtown Rocket',
        '逃げるは恥だが役に立つ' => 'Huir es vergonzoso pero útil',
        '東京ラブストーリー' => 'Historia de amor en Tokio',
        
        // Títulos en español para dramas chinos populares
        '台灣龍捲風' => 'Tornado de Taiwán',
        '琅琊榜' => 'Langya Bang',
        '三生三世十里桃花' => 'Amor eterno',
        '陳情令' => 'La Untamed',
        '香蜜沉沉烬如霜' => 'Ashes of Love',
        '仙剑奇侠传' => 'La leyenda de la espada antigua',
        '延禧攻略' => 'Historia del Palacio Yanxi',
    ];

    /**
     * Map of translated descriptions
     */
    protected $descriptionTranslations = [
        // Ejemplo de traducciones en español (añadir según necesidad)
        'In a magical land' => 'En una tierra mágica',
        'A story of love and sacrifice' => 'Una historia de amor y sacrificio',
        'Two people from different worlds' => 'Dos personas de mundos diferentes',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando la traducción de títulos importados a español...');

        // Obtener todos los títulos importados
        $titles = Title::all();
        $count = 0;

        $this->output->progressStart($titles->count());
        
        foreach ($titles as $title) {
            $updated = false;
            
            // Traducir título si existe en nuestro mapeo
            if (isset($this->titleTranslations[$title->original_title])) {
                $title->title = $this->titleTranslations[$title->original_title];
                $updated = true;
                $this->line("Título traducido: {$title->original_title} -> {$title->title}");
            } 
            // Si no tenemos traducción, usamos el título en inglés si está disponible o dejamos el original
            else if ($title->title == $title->original_title && 
                     preg_match('/[\x{3040}-\x{30ff}\x{3400}-\x{4dbf}\x{4e00}-\x{9fff}\x{f900}-\x{faff}\x{ac00}-\x{d7af}]/u', $title->title)) {
                
                // Si el título está en caracteres asiáticos y no tenemos traducción, 
                // generamos un slug legible o usamos el ID
                $title->title = "Drama " . ($title->category ? $title->category->name : 'Asiático') . " #" . $title->id;
                $updated = true;
                $this->line("Título por defecto: {$title->original_title} -> {$title->title}");
            }
            
            // Asegurarnos de que el slug sea válido
            if (empty($title->slug) || $title->slug == 'drama-' . $title->tmdb_id) {
                // Generar slug a partir del título español
                $title->slug = Str::slug($title->title);
                
                // Si el slug existe, añadir un sufijo único
                $i = 1;
                $originalSlug = $title->slug;
                while (DB::table('titles')->where('id', '!=', $title->id)->where('slug', $title->slug)->exists()) {
                    $title->slug = $originalSlug . '-' . $i++;
                }
                
                $updated = true;
                $this->line("Slug generado: {$title->slug}");
            }
            
            // Procesar la descripción si está vacía o en otro idioma
            if (empty($title->description) && !empty($title->original_overview)) {
                $title->description = $title->original_overview;
                $updated = true;
                $this->line("Descripción actualizada para: {$title->title}");
            }
            
            // Añadir release_year si no existe 
            if (empty($title->release_year) && !empty($title->release_date)) {
                $title->release_year = date('Y', strtotime($title->release_date));
                $updated = true;
            }
            
            // Establecer duración predeterminada si es necesario
            if (empty($title->runtime) && $title->type == 'series') {
                $title->runtime = 60; // 60 minutos para series por defecto
                $updated = true;
            }
            
            // Guardar si hay cambios
            if ($updated) {
                $title->save();
                $count++;
            }
            
            $this->output->progressAdvance();
        }
        
        $this->output->progressFinish();
        $this->info("Se han traducido o actualizado {$count} títulos.");
        
        // Actualizar la columna is_featured para asegurarnos de tener contenido destacado
        $this->info('Marcando títulos populares como destacados...');
        $featured = DB::table('titles')
            ->orderBy('popularity', 'desc')
            ->limit(10)
            ->update(['is_featured' => true]);
        
        $this->info("Se han marcado {$featured} títulos como destacados.");
        
        return Command::SUCCESS;
    }
}