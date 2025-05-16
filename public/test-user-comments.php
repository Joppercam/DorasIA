<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\User;
use App\Models\Profile;
use App\Models\Title;

// Create test user if not exists
$user = User::firstOrCreate(
    ['email' => 'test@example.com'],
    [
        'name' => 'Test User',
        'password' => bcrypt('password'),
        'email_verified_at' => now()
    ]
);

echo "User: {$user->name} (ID: {$user->id})\n";

// Create profile if not exists
$profile = Profile::firstOrCreate(
    ['user_id' => $user->id],
    [
        'name' => 'Test Profile',
        'slug' => 'test-profile',
        'bio' => 'Test profile for testing'
    ]
);

echo "Profile: {$profile->name} (ID: {$profile->id})\n";

// Make this the active profile
\Illuminate\Support\Facades\Session::put('active_profile_id', $profile->id);
auth()->login($user);

// Get first title
$title = Title::first();
if (!$title) {
    echo "No titles found.\n";
    exit;
}

echo "Title: {$title->title} (ID: {$title->id})\n";

// Create a test comment
try {
    $comment = $title->comments()->create([
        'profile_id' => $profile->id,
        'content' => 'Test comment from script at ' . now()->toDateTimeString()
    ]);
    echo "Comment created successfully! ID: {$comment->id}\n";
} catch (\Exception $e) {
    echo "Error creating comment: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

// List all comments for this title
echo "\nAll comments for this title:\n";
$comments = $title->comments()->with('profile')->get();
foreach ($comments as $comment) {
    echo "- [{$comment->id}] {$comment->content} (by {$comment->profile->name})\n";
}