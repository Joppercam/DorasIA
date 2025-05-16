<?php
require_once __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Basic test to see the correct URL generation
echo "Testing profile edit routes:\n\n";

// Load the profile
$profile = \App\Models\Profile::find(2);
if ($profile) {
    echo "Profile ID: " . $profile->id . "\n";
    echo "Profile Name: " . $profile->name . "\n";
    echo "Profile User ID: " . $profile->user_id . "\n";
    echo "Edit URL: " . route('profiles.edit', $profile) . "\n";
} else {
    echo "Profile not found\n";
}