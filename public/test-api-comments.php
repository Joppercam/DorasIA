<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Title;

// Get test user
$user = User::where('email', 'test@example.com')->first();
if (!$user) {
    echo "Test user not found.\n";
    exit;
}

// Get first title
$title = Title::first();
if (!$title) {
    echo "No titles found.\n";
    exit;
}

echo "Testing API for Title: {$title->title} (ID: {$title->id})\n";

// Create a request to the API
$request = Request::create(
    "/api/titles/{$title->id}/comments",
    'GET',
    ['sort' => 'newest', 'mine' => 'false', 'page' => 1]
);

// Authenticate the request
$request->setUserResolver(function () use ($user) {
    return $user;
});

// Set active profile in session
\Illuminate\Support\Facades\Session::put('active_profile_id', $user->getActiveProfile()?->id);

try {
    // Handle the request
    $response = $kernel->handle($request);
    
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Content: " . $response->getContent() . "\n";
    
    if ($response->getStatusCode() !== 200) {
        echo "Error: Unexpected status code\n";
    } else {
        $data = json_decode($response->getContent(), true);
        echo "Comments count: " . count($data['data'] ?? []) . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}