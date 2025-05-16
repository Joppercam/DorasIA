<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Get first user
$user = User::first();
Auth::login($user);

// Get active profile
$activeProfile = $user->getActiveProfile();

echo "User: " . $user->email . " (ID: " . $user->id . ")\n";
echo "Active Profile: " . ($activeProfile ? $activeProfile->name . " (ID: " . $activeProfile->id . ")" : "None") . "\n";

// Generate the edit URL
if ($activeProfile) {
    $editUrl = route('profiles.edit', $activeProfile);
    echo "Edit URL: " . $editUrl . "\n";
    
    // Parse the URL to get the profile ID
    $pattern = '/profiles\/(\d+)\/edit/';
    if (preg_match($pattern, $editUrl, $matches)) {
        $profileId = $matches[1];
        echo "Profile ID in URL: " . $profileId . "\n";
        
        // Load that profile
        $urlProfile = \App\Models\Profile::find($profileId);
        if ($urlProfile) {
            echo "URL Profile belongs to user: " . $urlProfile->user_id . "\n";
            echo "Current user ID: " . $user->id . "\n";
            echo "Do they match? " . ($urlProfile->user_id == $user->id ? "YES" : "NO") . "\n";
        }
    }
} else {
    echo "No active profile found\n";
}

// Let's check all profiles for this user
echo "\n=== All User Profiles ===\n";
$profiles = $user->profiles;
foreach ($profiles as $profile) {
    echo "Profile: " . $profile->name . " (ID: " . $profile->id . ", User ID: " . $profile->user_id . ")\n";
}