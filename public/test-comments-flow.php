<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Title;
use App\Models\User;
use App\Models\Profile;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Check database connection
echo "=== DATABASE CHECK ===\n";
try {
    DB::connection()->getPdo();
    echo "Database connected: " . config('database.default') . "\n";
} catch (\Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Check tables exist
echo "\n=== TABLES CHECK ===\n";
$tables = ['titles', 'comments', 'profiles', 'users'];
foreach ($tables as $table) {
    $exists = DB::select("SELECT count(*) as count FROM sqlite_master WHERE type='table' AND name=?", [$table]);
    echo "Table '$table': " . ($exists[0]->count > 0 ? "EXISTS" : "NOT FOUND") . "\n";
}

// Check a title exists
echo "\n=== TITLE CHECK ===\n";
$title = Title::first();
if (!$title) {
    echo "No titles found in database\n";
    exit(1);
}
echo "Using title ID: " . $title->id . " - " . $title->title . "\n";

// Check if we have users and profiles
echo "\n=== USER/PROFILE CHECK ===\n";
$user = User::first();
if (!$user) {
    echo "No users found\n";
    exit(1);
}
echo "Using user ID: " . $user->id . " - " . $user->email . "\n";

$profile = $user->profiles()->first();
if (!$profile) {
    echo "User has no profiles\n";
    echo "Creating a test profile...\n";
    $profile = $user->profiles()->create([
        'name' => 'Test Profile',
        'country' => 'US',
        'language' => 'es',
        'age_group' => '25-34',
        'preferred_genres' => ['drama', 'comedy'],
        'avatar_url' => '/images/profiles/avatar1.jpg'
    ]);
}
echo "Using profile ID: " . $profile->id . " - " . $profile->name . "\n";

// Check comments endpoint WITHOUT auth
echo "\n=== COMMENTS ENDPOINT TEST (NO AUTH) ===\n";
$endpoint = "http://localhost:8000/api/titles/" . $title->id . "/comments";
echo "Testing endpoint: " . $endpoint . "\n";

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);
echo "Response Body:\n" . substr($body, 0, 500) . "\n";

// Test with PHP directly (bypassing HTTP)
echo "\n=== DIRECT PHP TEST ===\n";
try {
    $controller = new \App\Http\Controllers\Api\CommentController();
    $request = new \Illuminate\Http\Request();
    $request->query->set('sort', 'newest');
    $request->query->set('page', 1);
    
    $result = $controller->index($title, $request);
    $data = json_decode($result->content(), true);
    
    echo "Direct call successful!\n";
    echo "Comments count: " . count($data['data'] ?? []) . "\n";
    echo "Has more: " . ($data['has_more'] ? 'true' : 'false') . "\n";
} catch (\Exception $e) {
    echo "Direct call failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

// Check the comment relationship
echo "\n=== COMMENT RELATIONSHIP TEST ===\n";
try {
    echo "Title has comments() method: " . (method_exists($title, 'comments') ? 'YES' : 'NO') . "\n";
    $comments = $title->comments()->get();
    echo "Comments count: " . $comments->count() . "\n";
    
    // Create a test comment
    echo "\nCreating test comment...\n";
    $comment = $title->comments()->create([
        'profile_id' => $profile->id,
        'content' => 'Test comment from debug script',
    ]);
    echo "Test comment created with ID: " . $comment->id . "\n";
    
    // Test loading the comment with relationships
    $comment->load(['profile.user', 'likes']);
    $comment->loadCount(['replies', 'likes']);
    echo "Comment profile: " . $comment->profile->name . "\n";
    echo "Likes count: " . $comment->likes_count . "\n";
    echo "Replies count: " . $comment->replies_count . "\n";
    
} catch (\Exception $e) {
    echo "Relationship test failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

// Check middleware behavior
echo "\n=== MIDDLEWARE CHECK ===\n";
$app = app();
$request = \Illuminate\Http\Request::create('/api/titles/' . $title->id . '/comments', 'GET');
$request->headers->set('Accept', 'application/json');

try {
    $middleware = new \App\Http\Middleware\HasActiveProfile();
    $response = $middleware->handle($request, function($req) {
        return response()->json(['middleware' => 'passed']);
    });
    
    if ($response->getStatusCode() === 403) {
        echo "Middleware blocked the request\n";
        echo "Response: " . $response->getContent() . "\n";
    } else {
        echo "Middleware allowed the request\n";
    }
} catch (\Exception $e) {
    echo "Middleware check failed: " . $e->getMessage() . "\n";
}

echo "\n=== DONE ===\n";