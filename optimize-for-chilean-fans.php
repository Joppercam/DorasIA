<?php

// Script para optimizar contenido específicamente para fanáticas chilenas de K-dramas

require_once 'vendor/autoload.php';

echo "🇨🇱 Optimizando Dorasia para fanáticas chilenas de K-dramas...\n\n";

// Crear contenido específico para Chile
$chileanContentPath = 'resources/views/components/chilean-content.blade.php';
$chileanContentTemplate = '<div class="chilean-features">
    <div class="feature-banner" style="background: linear-gradient(135deg, #e50914 0%, #8b0000 100%); padding: 2rem; border-radius: 12px; margin: 2rem 0; text-align: center;">
        <h3 style="color: white; margin-bottom: 1rem; font-size: 1.5rem;">🇨🇱 Dorasia Chile - Tu portal K-drama</h3>
        <p style="color: #fff; opacity: 0.9; font-size: 1.1rem;">
            Descubre los mejores dramas coreanos con contenido adaptado especialmente para las fanáticas chilenas
        </p>
    </div>

    <div class="chile-specific-sections" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin: 2rem 0;">
        <div class="feature-card" style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
            <h4 style="color: #e50914; margin-bottom: 1rem;">📺 K-dramas Trending en Chile</h4>
            <p style="color: #ccc; line-height: 1.5;">
                Series populares entre las fanáticas chilenas, con reseñas y recomendaciones locales.
            </p>
        </div>

        <div class="feature-card" style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
            <h4 style="color: #e50914; margin-bottom: 1rem;">💬 Lenguaje Adaptado</h4>
            <p style="color: #ccc; line-height: 1.5;">
                Traducciones y descripciones en español chileno, manteniendo términos K-drama familiares.
            </p>
        </div>

        <div class="feature-card" style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
            <h4 style="color: #e50914; margin-bottom: 1rem;">⭐ Recomendaciones Locales</h4>
            <p style="color: #ccc; line-height: 1.5;">
                Sugerencias basadas en los gustos y preferencias de la comunidad K-drama chilena.
            </p>
        </div>
    </div>
</div>';

file_put_contents($chileanContentPath, $chileanContentTemplate);
echo "✅ Componente de contenido chileno creado\n";

// Actualizar el footer con información local
$footerPath = 'resources/views/components/footer.blade.php';
$footerContent = '<footer style="background: #0a0a0a; padding: 3rem 4% 2rem; margin-top: 4rem; border-top: 1px solid rgba(255,255,255,0.1);">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
        <div>
            <h4 style="color: #e50914; margin-bottom: 1rem; font-size: 1.2rem;">DORASIA Chile</h4>
            <p style="color: #ccc; line-height: 1.6; margin-bottom: 1rem;">
                La plataforma #1 de K-dramas para fanáticas chilenas. Descubre, explora y disfruta los mejores dramas coreanos.
            </p>
            <div style="display: flex; gap: 1rem;">
                <span style="background: rgba(255,255,255,0.1); padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem; color: #ccc;">
                    🇨🇱 Hecho en Chile
                </span>
                <span style="background: rgba(255,255,255,0.1); padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem; color: #ccc;">
                    🇰🇷 K-drama Lover
                </span>
            </div>
        </div>

        <div>
            <h4 style="color: white; margin-bottom: 1rem;">Géneros Populares</h4>
            <ul style="list-style: none; padding: 0; color: #ccc; line-height: 2;">
                <li><a href="#romance" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Romance</a></li>
                <li><a href="#drama" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Drama</a></li>
                <li><a href="#comedia" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Comedia Romántica</a></li>
                <li><a href="#historicos" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Dramas Históricos</a></li>
                <li><a href="#misterio" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Misterio & Suspenso</a></li>
            </ul>
        </div>

        <div>
            <h4 style="color: white; margin-bottom: 1rem;">Comunidad</h4>
            <ul style="list-style: none; padding: 0; color: #ccc; line-height: 2;">
                <li>📱 Síguenos en redes sociales</li>
                <li>💬 Únete a nuestra comunidad</li>
                <li>⭐ Comparte tus reseñas</li>
                <li>📧 Newsletter semanal</li>
            </ul>
        </div>

        <div>
            <h4 style="color: white; margin-bottom: 1rem;">Para Fanáticas</h4>
            <ul style="list-style: none; padding: 0; color: #ccc; line-height: 2;">
                <li>🎭 Guías de actores</li>
                <li>📺 Recomendaciones personalizadas</li>
                <li>🏆 Rankings actualizados</li>
                <li>📅 Calendario de estrenos</li>
                <li>💖 Lista de favoritos</li>
            </ul>
        </div>
    </div>

    <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem; text-align: center;">
        <p style="color: #666; font-size: 0.9rem; margin-bottom: 0.5rem;">
            © 2024 Dorasia Chile - La mejor plataforma de K-dramas para fanáticas chilenas
        </p>
        <p style="color: #555; font-size: 0.8rem;">
            Hecho con 💜 para la comunidad K-drama en Chile | Todos los derechos de las series pertenecen a sus respectivos creadores
        </p>
    </div>
</footer>

<style>
footer a:hover {
    color: #e50914 !important;
}

@media (max-width: 768px) {
    footer {
        padding: 2rem 2% 1.5rem !important;
    }
    
    footer > div:first-child {
        grid-template-columns: 1fr !important;
        gap: 1.5rem !important;
    }
}
</style>';

file_put_contents($footerPath, $footerContent);
echo "✅ Footer chileno creado\n";

// Crear archivo de configuraciones específicas para Chile
$chileanConfigPath = 'config/chilean.php';
$chileanConfig = '<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuraciones específicas para Chile
    |--------------------------------------------------------------------------
    */

    \'timezone\' => \'America/Santiago\',
    
    \'currency\' => \'CLP\',
    
    \'locale\' => \'es_CL\',

    /*
    |--------------------------------------------------------------------------
    | Términos K-drama populares en Chile
    |--------------------------------------------------------------------------
    */
    \'kdrama_terms\' => [
        \'oppa\' => \'Oppa\',
        \'unnie\' => \'Unnie\', 
        \'chaebol\' => \'Chaebol\',
        \'aegyo\' => \'Aegyo\',
        \'hallyu\' => \'Hallyu\',
        \'sageuk\' => \'Sageuk\',
        \'makjang\' => \'Makjang\',
        \'noona\' => \'Noona\',
        \'sunbae\' => \'Sunbae\',
        \'hoobae\' => \'Hoobae\'
    ],

    /*
    |--------------------------------------------------------------------------
    | Traducciones específicas chilenas
    |--------------------------------------------------------------------------
    */
    \'chilean_translations\' => [
        \'romance\' => \'Romance\',
        \'drama\' => \'Drama\',
        \'comedy\' => \'Comedia\',
        \'historical\' => \'Histórico (Sageuk)\',
        \'mystery\' => \'Misterio\',
        \'action\' => \'Acción\',
        \'fantasy\' => \'Fantasía\',
        \'school\' => \'Escolar\',
        \'medical\' => \'Médico\',
        \'legal\' => \'Legal\',
        \'thriller\' => \'Suspenso\',
        \'slice_of_life\' => \'Vida Cotidiana\'
    ],

    /*
    |--------------------------------------------------------------------------
    | Horarios de emisión populares
    |--------------------------------------------------------------------------
    */
    \'popular_viewing_times\' => [
        \'prime_time\' => [\'20:00\', \'22:00\'],
        \'weekend\' => [\'14:00\', \'18:00\', \'20:00\'],
        \'late_night\' => [\'22:00\', \'00:00\']
    ],

    /*
    |--------------------------------------------------------------------------
    | Géneros más populares en Chile
    |--------------------------------------------------------------------------
    */
    \'popular_genres\' => [
        \'Romance\',
        \'Drama\', 
        \'Comedia Romántica\',
        \'Dramas Históricos\',
        \'Misterio y Suspenso\',
        \'Médicos\',
        \'Escolares\',
        \'Fantasía\'
    ],

    /*
    |--------------------------------------------------------------------------
    | Frases y expresiones chilenas para la interfaz
    |--------------------------------------------------------------------------
    */
    \'chilean_phrases\' => [
        \'awesome\' => \'¡Bacán!\',
        \'amazing\' => \'¡Increíble!\',
        \'love_it\' => \'¡Me encanta!\',
        \'must_watch\' => \'Imperdible\',
        \'trending\' => \'Trending\',
        \'new_episode\' => \'Nuevo capítulo\',
        \'binge_watch\' => \'Maratón\',
        \'emotional\' => \'Emotivo\',
        \'romantic\' => \'Romántico\',
        \'dramatic\' => \'Dramático\'
    ]
];';

file_put_contents($chileanConfigPath, $chileanConfig);
echo "✅ Configuración chilena creada\n";

// Crear middleware para detectar ubicación chilena
$locationMiddlewarePath = 'app/Http/Middleware/ChileanLocalization.php';
$locationMiddleware = '<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class ChileanLocalization
{
    public function handle(Request $request, Closure $next)
    {
        // Detectar si el usuario es de Chile
        $isChilean = $this->detectChileanUser($request);
        
        if ($isChilean) {
            // Establecer configuraciones específicas para Chile
            App::setLocale(\'es_CL\');
            Session::put(\'user_country\', \'CL\');
            Session::put(\'is_chilean\', true);
            
            // Configurar timezone
            config([\'app.timezone\' => \'America/Santiago\']);
        }
        
        return $next($request);
    }
    
    private function detectChileanUser(Request $request): bool
    {
        // Verificar IP (simplificado - en producción usar servicio geolocation)
        $userAgent = $request->header(\'User-Agent\');
        $acceptLanguage = $request->header(\'Accept-Language\');
        
        // Detectar por idioma preferido
        if (str_contains($acceptLanguage, \'es-CL\') || str_contains($acceptLanguage, \'es_CL\')) {
            return true;
        }
        
        // Verificar si ya se detectó previamente
        if (Session::get(\'is_chilean\')) {
            return true;
        }
        
        // Por defecto asumir que es chileno para esta demo
        return true;
    }
}';

file_put_contents($locationMiddlewarePath, $locationMiddleware);
echo "✅ Middleware de localización chilena creado\n";

// Actualizar el archivo de rutas para incluir el middleware
$webRoutesPath = 'routes/web.php';
$webRoutes = file_get_contents($webRoutesPath);

// Agregar middleware al grupo de rutas si no existe
if (!str_contains($webRoutes, 'ChileanLocalization')) {
    $middlewareGroup = "\n// Aplicar localización chilena a todas las rutas\nRoute::middleware(['chilean.localization'])->group(function () {\n";
    $existingRoutes = $webRoutes;
    $newRoutes = "<?php\n\nuse Illuminate\Support\Facades\Route;\nuse App\Http\Controllers\HomeController;\nuse App\Http\Controllers\SeriesController;\n\n" . $middlewareGroup . "\n" . trim(str_replace(['<?php', 'use Illuminate\Support\Facades\Route;', 'use App\Http\Controllers\HomeController;', 'use App\Http\Controllers\SeriesController;'], '', $existingRoutes)) . "\n});\n";
    
    file_put_contents($webRoutesPath, $newRoutes);
    echo "✅ Middleware agregado a las rutas\n";
}

// Crear helper para contenido chileno
$chileanHelperPath = 'app/Helpers/ChileanHelper.php';
$chileanHelper = '<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;

class ChileanHelper
{
    /**
     * Verifica si el usuario es de Chile
     */
    public static function isChileanUser(): bool
    {
        return Session::get(\'is_chilean\', true);
    }
    
    /**
     * Obtiene términos K-drama adaptados para Chile
     */
    public static function getKdramaTerm(string $term): string
    {
        $terms = config(\'chilean.kdrama_terms\', []);
        return $terms[$term] ?? $term;
    }
    
    /**
     * Obtiene traducciones específicas chilenas
     */
    public static function getChileanTranslation(string $key): string
    {
        $translations = config(\'chilean.chilean_translations\', []);
        return $translations[$key] ?? $key;
    }
    
    /**
     * Obtiene frases chilenas para la interfaz
     */
    public static function getChileanPhrase(string $key): string
    {
        $phrases = config(\'chilean.chilean_phrases\', []);
        return $phrases[$key] ?? $key;
    }
    
    /**
     * Formatea fecha para horario chileno
     */
    public static function formatChileanDate($date, string $format = \'d/m/Y H:i\'): string
    {
        if (!$date) return \'\';
        
        return \Carbon\Carbon::parse($date)
            ->setTimezone(\'America/Santiago\')
            ->format($format);
    }
    
    /**
     * Obtiene géneros populares en Chile
     */
    public static function getPopularGenres(): array
    {
        return config(\'chilean.popular_genres\', []);
    }
    
    /**
     * Personaliza el saludo según la hora chilena
     */
    public static function getChileanGreeting(): string
    {
        $hour = \Carbon\Carbon::now(\'America/Santiago\')->hour;
        
        if ($hour < 12) {
            return \'¡Buenos días, fanática!\';
        } elseif ($hour < 18) {
            return \'¡Buenas tardes!\';
        } else {
            return \'¡Buenas noches, hora de K-dramas!\';
        }
    }
}';

file_put_contents($chileanHelperPath, $chileanHelper);
echo "✅ Helper chileno creado\n";

// Agregar el helper al composer.json
$composerPath = 'composer.json';
$composer = json_decode(file_get_contents($composerPath), true);

if (!isset($composer['autoload']['files'])) {
    $composer['autoload']['files'] = [];
}

if (!in_array('app/Helpers/ChileanHelper.php', $composer['autoload']['files'])) {
    $composer['autoload']['files'][] = 'app/Helpers/ChileanHelper.php';
    file_put_contents($composerPath, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "✅ Helper agregado al autoload\n";
}

echo "\n🇨🇱 ¡Optimización para fanáticas chilenas completada!\n";
echo "\n📋 Características implementadas:\n";
echo "✅ Contenido adaptado para Chile\n";
echo "✅ Footer con información local\n";
echo "✅ Configuraciones específicas chilenas\n";
echo "✅ Middleware de localización\n";
echo "✅ Helper para funciones chilenas\n";
echo "✅ Términos K-drama familiares\n";
echo "✅ Traducciones en español chileno\n";
echo "\n💡 Próximos pasos:\n";
echo "1. composer dump-autoload\n";
echo "2. Registrar middleware en app/Http/Kernel.php\n";
echo "3. Incluir componentes en las vistas\n";
echo "4. Configurar API de geolocalización para detección precisa\n";