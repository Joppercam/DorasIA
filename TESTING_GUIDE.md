# Guía de Testing - Dorasia

Esta guía documenta las estrategias de testing implementadas en el proyecto Dorasia y proporciona ejemplos para escribir nuevos tests.

## Índice

1. [Configuración del Entorno de Testing](#configuración-del-entorno-de-testing)
2. [Tipos de Tests](#tipos-de-tests)
3. [Tests Unitarios](#tests-unitarios)
4. [Tests de Integración](#tests-de-integración)
5. [Tests de API](#tests-de-api)
6. [Tests de Navegador](#tests-de-navegador)
7. [Tests de Rendimiento](#tests-de-rendimiento)
8. [Mejores Prácticas](#mejores-prácticas)
9. [Cobertura de Tests](#cobertura-de-tests)
10. [CI/CD](#cicd)

## Configuración del Entorno de Testing

### Requisitos

- PHPUnit 10.x
- Laravel Dusk
- Mockery
- Faker
- SQLite (para tests de base de datos)

### Configuración Inicial

1. Instalar dependencias de desarrollo:
```bash
composer install --dev
npm install --save-dev
```

2. Copiar archivo de configuración de tests:
```bash
cp .env.example .env.testing
```

3. Configurar base de datos de testing en `.env.testing`:
```env
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
BROADCAST_DRIVER=log
CACHE_DRIVER=array
QUEUE_CONNECTION=sync
SESSION_DRIVER=array
```

4. Preparar Laravel Dusk (para tests de navegador):
```bash
php artisan dusk:install
```

## Tipos de Tests

### 1. Tests Unitarios
Prueban componentes individuales en aislamiento.

### 2. Tests de Integración
Verifican la interacción entre múltiples componentes.

### 3. Tests de API
Validan endpoints y respuestas de la API.

### 4. Tests de Navegador
Simulan interacciones de usuario en el navegador.

### 5. Tests de Rendimiento
Miden tiempos de respuesta y uso de recursos.

## Tests Unitarios

### Ejemplo: Test de Modelo User

```php
<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_profile()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Profile::class, $user->profile);
        $this->assertEquals($profile->id, $user->profile->id);
    }

    public function test_user_can_have_many_comments()
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->comments);
        $this->assertContainsOnlyInstancesOf(Comment::class, $user->comments);
    }

    public function test_user_full_name_attribute()
    {
        $user = User::factory()->create([
            'name' => 'John Doe'
        ]);

        $this->assertEquals('John Doe', $user->full_name);
    }
}
```

### Ejemplo: Test de Servicio CacheService

```php
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CacheService;
use App\Models\Title;
use Illuminate\Support\Facades\Cache;

class CacheServiceTest extends TestCase
{
    public function test_cache_service_remembers_title()
    {
        $title = Title::factory()->create();
        
        // Primera llamada - debe ejecutar el callback
        $cachedTitle = CacheService::rememberTitle($title->id, function() use ($title) {
            return $title;
        });

        $this->assertEquals($title->id, $cachedTitle->id);

        // Segunda llamada - debe recuperar del caché
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn($title);

        $cachedTitle2 = CacheService::rememberTitle($title->id, function() {
            $this->fail('El callback no debería ejecutarse');
        });

        $this->assertEquals($title->id, $cachedTitle2->id);
    }

    public function test_cache_invalidation_on_update()
    {
        $title = Title::factory()->create();
        $cacheKey = 'title_' . $title->id;

        // Cachear el título
        Cache::put($cacheKey, $title, 3600);

        // Actualizar el título
        $title->update(['title' => 'Nuevo Título']);

        // Verificar que el caché fue invalidado
        $this->assertNull(Cache::get($cacheKey));
    }
}
```

## Tests de Integración

### Ejemplo: Test de ProfileController

```php
<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_own_profile()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get(route('profiles.show', $profile));

        $response->assertStatus(200);
        $response->assertViewIs('profiles.show');
        $response->assertViewHas('profile', $profile);
    }

    public function test_user_can_update_own_profile()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->put(route('profiles.update', $profile), [
                'username' => 'newusername',
                'bio' => 'Nueva biografía',
                'location' => 'Madrid, España'
            ]);

        $response->assertRedirect(route('profiles.show', $profile));
        
        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
            'username' => 'newusername',
            'bio' => 'Nueva biografía'
        ]);
    }

    public function test_user_cannot_update_other_profile()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherProfile = Profile::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)
            ->put(route('profiles.update', $otherProfile), [
                'username' => 'hackedusername'
            ]);

        $response->assertStatus(403);
    }
}
```

### Ejemplo: Test de Sistema de Seguimiento

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Notifications\NewFollower;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FollowSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_follow_another_user()
    {
        Notification::fake();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $profile1 = Profile::factory()->create(['user_id' => $user1->id]);
        $profile2 = Profile::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)
            ->post(route('api.profiles.follow', $profile2));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_following' => true
        ]);

        $this->assertTrue($profile1->isFollowing($profile2));
        $this->assertEquals(1, $profile2->followers()->count());

        // Verificar que se envió la notificación
        Notification::assertSentTo(
            $user2,
            NewFollower::class,
            function ($notification) use ($profile1) {
                return $notification->follower->id === $profile1->id;
            }
        );
    }
}
```

## Tests de API

### Ejemplo: Test de Endpoints de Títulos

```php
<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Title;
use App\Models\Genre;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TitleApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(GenreSeeder::class);
    }

    public function test_can_list_titles_with_filters()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $genre = Genre::first();
        $titles = Title::factory()->count(5)->create();
        $titles->first()->genres()->attach($genre);

        $response = $this->getJson('/api/titles?' . http_build_query([
            'genre' => $genre->id,
            'sort' => 'popular',
            'page' => 1
        ]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'original_title',
                        'type',
                        'year',
                        'poster_url',
                        'genres'
                    ]
                ],
                'meta' => [
                    'current_page',
                    'total'
                ]
            ]);
    }

    public function test_can_get_single_title_with_details()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $title = Title::factory()->create();
        $genre = Genre::first();
        $title->genres()->attach($genre);

        $response = $this->getJson("/api/titles/{$title->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $title->id,
                'title' => $title->title,
                'genres' => [
                    [
                        'id' => $genre->id,
                        'name' => $genre->name
                    ]
                ]
            ]);
    }

    public function test_requires_authentication()
    {
        $response = $this->getJson('/api/titles');
        $response->assertStatus(401);
    }
}
```

### Ejemplo: Test de Rate Limiting

```php
<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_rate_limiting()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Hacer 60 requests (el límite)
        for ($i = 0; $i < 60; $i++) {
            $response = $this->getJson('/api/titles');
            $response->assertStatus(200);
        }

        // La request 61 debe ser rechazada
        $response = $this->getJson('/api/titles');
        $response->assertStatus(429); // Too Many Requests
        $response->assertHeader('X-RateLimit-Limit', 60);
        $response->assertHeader('X-RateLimit-Remaining', 0);
    }
}
```

## Tests de Navegador

### Ejemplo: Test de Registro con Laravel Dusk

```php
<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Models\User;

class RegistrationTest extends DuskTestCase
{
    public function test_user_can_register()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('name', 'Test User')
                ->type('email', 'test@example.com')
                ->type('password', 'password123')
                ->type('password_confirmation', 'password123')
                ->check('terms')
                ->press('Crear cuenta')
                ->assertPathIs('/dashboard')
                ->assertAuthenticated();
        });

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }

    public function test_registration_validation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->press('Crear cuenta')
                ->assertSee('El campo nombre es requerido')
                ->assertSee('El campo email es requerido')
                ->assertSee('El campo contraseña es requerido');
        });
    }
}
```

### Ejemplo: Test de Reproducción de Video

```php
<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Models\User;
use App\Models\Title;

class VideoPlayerTest extends DuskTestCase
{
    public function test_user_can_play_video()
    {
        $user = User::factory()->create();
        $title = Title::factory()->create([
            'type' => 'movie',
            'video_url' => 'https://example.com/video.mp4'
        ]);

        $this->browse(function (Browser $browser) use ($user, $title) {
            $browser->loginAs($user)
                ->visit("/titles/{$title->id}")
                ->press('Ver ahora')
                ->waitFor('.video-player')
                ->assertVisible('.play-button')
                ->click('.play-button')
                ->pause(2000)
                ->assertMissing('.play-button')
                ->assertVisible('.pause-button');
        });
    }

    public function test_video_progress_is_saved()
    {
        $user = User::factory()->create();
        $title = Title::factory()->create();

        $this->browse(function (Browser $browser) use ($user, $title) {
            $browser->loginAs($user)
                ->visit("/watch/{$title->id}")
                ->waitFor('.video-player')
                ->click('.play-button')
                ->pause(5000) // Ver 5 segundos
                ->visit('/') // Salir
                ->visit("/watch/{$title->id}") // Volver
                ->assertValue('.progress-bar', '5'); // Verificar progreso guardado
        });
    }
}
```

## Tests de Rendimiento

### Ejemplo: Test de Carga con PHPUnit

```php
<?php

namespace Tests\Performance;

use Tests\TestCase;
use App\Models\Title;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DatabasePerformanceTest extends TestCase
{
    public function test_catalog_query_performance()
    {
        // Crear datos de prueba
        Title::factory()->count(1000)->create();

        // Habilitar log de queries
        DB::enableQueryLog();

        $startTime = microtime(true);

        // Ejecutar query del catálogo
        $titles = Title::with(['genres', 'ratings'])
            ->withAvg('ratings', 'value')
            ->paginate(20);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // en milisegundos

        $queries = DB::getQueryLog();

        // Verificar rendimiento
        $this->assertLessThan(100, $executionTime, 'La consulta tardó más de 100ms');
        $this->assertLessThan(5, count($queries), 'Se ejecutaron demasiadas queries');
    }

    public function test_homepage_load_time()
    {
        $user = User::factory()->create();

        $startTime = microtime(true);
        
        $response = $this->actingAs($user)->get('/');
        
        $endTime = microtime(true);
        $loadTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(500, $loadTime, 'La página tardó más de 500ms en cargar');
    }
}
```

### Ejemplo: Test de Concurrencia

```php
<?php

namespace Tests\Performance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Title;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;

class ConcurrencyTest extends TestCase
{
    public function test_concurrent_rating_updates()
    {
        $title = Title::factory()->create();
        $users = User::factory()->count(10)->create();

        $results = [];

        // Simular 10 usuarios calificando al mismo tiempo
        foreach ($users as $user) {
            $results[] = $this->actingAs($user)->postJson("/api/titles/{$title->id}/rate", [
                'value' => rand(1, 10)
            ]);
        }

        // Verificar que todas las calificaciones se guardaron
        $this->assertEquals(10, $title->ratings()->count());

        // Verificar integridad de datos
        foreach ($results as $response) {
            $response->assertStatus(200);
        }
    }
}
```

## Mejores Prácticas

### 1. Organización de Tests

```
tests/
├── Unit/
│   ├── Models/
│   ├── Services/
│   └── Helpers/
├── Feature/
│   ├── Api/
│   ├── Controllers/
│   └── Auth/
├── Browser/
└── Performance/
```

### 2. Nomenclatura

- Prefija métodos de test con `test_`
- Usa nombres descriptivos: `test_user_can_update_own_profile`
- Agrupa tests relacionados en la misma clase

### 3. Fixtures y Factories

```php
// Usa factories para crear datos de prueba
$user = User::factory()->create();
$titles = Title::factory()->count(5)->create();

// Crea estados específicos
$publishedTitle = Title::factory()->published()->create();
$draftTitle = Title::factory()->draft()->create();
```

### 4. Assertions Personalizadas

```php
// En TestCase.php
protected function assertUserCanAccessRoute($user, $route)
{
    $response = $this->actingAs($user)->get($route);
    $response->assertStatus(200);
}

// En tests
$this->assertUserCanAccessRoute($admin, '/admin/dashboard');
```

### 5. Test Doubles

```php
// Mock de servicios externos
$tmdbMock = $this->mock(TmdbService::class);
$tmdbMock->shouldReceive('searchTitles')
    ->once()
    ->with('Parasite')
    ->andReturn($fakeResults);

// Spy para verificar llamadas
$cacheSpy = $this->spy(CacheService::class);
// ... ejecutar código
$cacheSpy->shouldHaveReceived('remember')->once();
```

### 6. Datos de Prueba

```php
// Usa seeders para tests
class TestDataSeeder extends Seeder
{
    public function run()
    {
        $genres = Genre::factory()->count(10)->create();
        $titles = Title::factory()->count(50)->create();
        
        // Relacionar datos
        $titles->each(function ($title) use ($genres) {
            $title->genres()->attach(
                $genres->random(rand(1, 3))
            );
        });
    }
}
```

## Cobertura de Tests

### Ejecutar Tests con Cobertura

```bash
# Generar reporte de cobertura
php artisan test --coverage

# Generar HTML detallado
php artisan test --coverage-html=coverage
```

### Métricas de Cobertura

Objetivos mínimos:
- Cobertura total: 80%
- Modelos: 90%
- Controladores: 85%
- Servicios: 95%
- Helpers: 90%

### Ejemplo de Reporte

```
 Models ........................... 92.5%
 Controllers ...................... 86.3%
 Services ......................... 94.8%
 Api .............................. 89.2%
 
 Total ............................ 88.7%
```

## CI/CD

### GitHub Actions

```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_DATABASE: dorasia_test
          MYSQL_ROOT_PASSWORD: password
        ports:
          - 3306:3306

    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        extensions: mbstring, mysql
        coverage: xdebug

    - name: Install Dependencies
      run: |
        composer install --no-progress --prefer-dist --optimize-autoloader
        npm ci
        npm run build

    - name: Prepare Laravel
      run: |
        cp .env.testing .env
        php artisan key:generate
        php artisan migrate

    - name: Execute tests
      run: php artisan test --coverage --min=80

    - name: Execute Dusk tests
      run: |
        php artisan dusk:chrome-driver
        php artisan serve &
        php artisan dusk

    - name: Upload coverage
      uses: codecov/codecov-action@v1
      with:
        file: ./coverage.xml
```

### Pre-commit Hooks

```bash
# .git/hooks/pre-commit
#!/bin/sh

# Ejecutar tests antes de commit
php artisan test --stop-on-failure

if [ $? -ne 0 ]; then
    echo "Tests fallaron. Commit cancelado."
    exit 1
fi

# Verificar estándares de código
./vendor/bin/phpcs --standard=PSR12 app/

if [ $? -ne 0 ]; then
    echo "Código no cumple PSR-12. Commit cancelado."
    exit 1
fi
```

## Debugging Tests

### Técnicas de Debug

```php
// Imprimir respuesta completa
$response->dump();
$response->dumpHeaders();
$response->dumpSession();

// Ver queries ejecutadas
DB::enableQueryLog();
// ... código
dd(DB::getQueryLog());

// Screenshots en Dusk
$browser->screenshot('mi-error');

// Pausar ejecución
$browser->pause(); // Dusk
$this->artisan('tinker'); // PHPUnit
```

### Logs de Test

```php
// En tests
Log::channel('testing')->info('Estado actual', [
    'user' => $user->toArray(),
    'title' => $title->toArray()
]);

// Configurar canal en config/logging.php
'testing' => [
    'driver' => 'single',
    'path' => storage_path('logs/testing.log'),
    'level' => 'debug',
],
```

## Conclusión

Una suite de tests completa es fundamental para mantener la calidad del código. Sigue estas guías para:

1. Escribir tests mantenibles y confiables
2. Alcanzar alta cobertura de código
3. Detectar problemas tempranamente
4. Facilitar refactoring seguro
5. Documentar comportamiento esperado

Recuerda: "Un código sin tests es código legacy desde el día uno."

---

Última actualización: Diciembre 2024