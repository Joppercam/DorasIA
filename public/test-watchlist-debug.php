<?php
session_start();
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

use App\Models\User;
use App\Models\Title;
use App\Models\Watchlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Start session
$app->make('session')->start();

// Get the user
$user = User::where('email', 'jpablo.basualdo@gmail.com')->first();

echo "<h1>Debug Watchlist</h1>";

echo "<h2>Usuario:</h2>";
echo "<pre>";
echo "Email: " . $user->email . "\n";
echo "ID: " . $user->id . "\n";
echo "</pre>";

echo "<h2>Perfiles:</h2>";
echo "<pre>";
foreach ($user->profiles as $profile) {
    echo "- {$profile->name} (ID: {$profile->id})\n";
}
echo "</pre>";

// Simular login
Auth::login($user);

echo "<h2>Perfil activo (después de login):</h2>";
echo "<pre>";
$activeProfile = $user->getActiveProfile();
if ($activeProfile) {
    echo "Nombre: {$activeProfile->name}\n";
    echo "ID: {$activeProfile->id}\n";
} else {
    echo "Ninguno\n";
}
echo "</pre>";

echo "<h2>Session data:</h2>";
echo "<pre>";
echo "Session ID: " . session()->getId() . "\n";
echo "active_profile_id: " . session()->get('active_profile_id', 'none') . "\n";
echo "</pre>";

// Verificar autenticación
echo "<h2>Estado de autenticación:</h2>";
echo "<pre>";
echo "Autenticado: " . (Auth::check() ? 'Sí' : 'No') . "\n";
if (Auth::check()) {
    echo "Usuario ID: " . Auth::id() . "\n";
}
echo "</pre>";

// Probar acceso a una ruta con middleware
echo "<h2>Prueba de acceso a rutas con middleware:</h2>";
echo "<pre>";
try {
    $request = Request::create('/watchlist/toggle', 'POST', [
        'title_id' => 1
    ]);
    $request->headers->set('X-CSRF-TOKEN', csrf_token());
    $request->headers->set('Accept', 'application/json');
    
    // Set session
    $request->setSession($app->make('session.store'));
    
    $response = $app->handle($request);
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "</pre>";

// Verificar watchlist actual
echo "<h2>Watchlist actual del perfil:</h2>";
echo "<pre>";
if ($activeProfile) {
    $watchlistItems = Watchlist::where('profile_id', $activeProfile->id)->get();
    foreach ($watchlistItems as $item) {
        $title = Title::find($item->title_id);
        echo "- {$title->title} (ID: {$item->title_id})\n";
    }
} else {
    echo "No hay perfil activo\n";
}
echo "</pre>";

// CSRF token
echo "<h2>CSRF Token:</h2>";
echo "<pre>";
echo csrf_token();
echo "</pre>";
?>

<h2>Prueba JavaScript:</h2>
<button onclick="testWatchlist()">Test Toggle</button>
<div id="result"></div>

<script>
function testWatchlist() {
    const resultDiv = document.getElementById('result');
    
    fetch('/watchlist/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            title_id: 1
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.text();
    })
    .then(text => {
        console.log('Response text:', text);
        resultDiv.innerHTML = '<pre>' + text + '</pre>';
        try {
            const data = JSON.parse(text);
            console.log('Parsed data:', data);
        } catch (e) {
            console.error('Failed to parse JSON:', e);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        resultDiv.innerHTML = '<pre>Error: ' + error.message + '</pre>';
    });
}
</script>