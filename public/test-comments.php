<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Title;
use App\Models\Comment;

// Get first title
$title = Title::first();

if (!$title) {
    echo "No titles found.\n";
    exit;
}

echo "Title: {$title->title}\n";
echo "ID: {$title->id}\n";

// Try to get comments
$comments = $title->comments()->get();
echo "Comments count: " . $comments->count() . "\n";

// Check if comments table has correct structure
$comment = new Comment();
$fillable = $comment->getFillable();
echo "Fillable fields: " . implode(', ', $fillable) . "\n";

// Test creating a comment if authenticated
$user = \App\Models\User::first();
if ($user && $user->getActiveProfile()) {
    $profile = $user->getActiveProfile();
    echo "Using profile: {$profile->name} (ID: {$profile->id})\n";
    
    $testComment = [
        'profile_id' => $profile->id,
        'content' => 'Test comment from test script'
    ];
    
    try {
        $comment = $title->comments()->create($testComment);
        echo "Comment created successfully! ID: {$comment->id}\n";
    } catch (\Exception $e) {
        echo "Error creating comment: " . $e->getMessage() . "\n";
    }
} else {
    echo "No user or active profile found for testing.\n";
}

// List existing comments
echo "\nExisting comments:\n";
foreach ($title->comments as $comment) {
    echo "- [{$comment->id}] {$comment->content} (by profile {$comment->profile_id})\n";
}