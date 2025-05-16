<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

// Get first user
$user = User::first();
if (!$user) {
    echo "No users found\n";
    exit(1);
}

echo "User: " . $user->email . " (ID: " . $user->id . ")\n";

// Get user's active profile
$activeProfile = $user->getActiveProfile();
if (!$activeProfile) {
    echo "User has no active profile\n";
    
    // Get any profile
    $profile = $user->profiles()->first();
    if ($profile) {
        echo "Found profile: " . $profile->name . " (ID: " . $profile->id . ")\n";
        echo "Profile user_id: " . $profile->user_id . "\n";
    } else {
        echo "User has no profiles at all\n";
    }
} else {
    echo "Active profile: " . $activeProfile->name . " (ID: " . $activeProfile->id . ")\n";
    echo "Profile user_id: " . $activeProfile->user_id . "\n";
}

// Test the policy
echo "\n=== Testing Policy ===\n";
if ($activeProfile) {
    Auth::login($user);
    
    // Test if user can update their own profile
    echo "Can user update profile? ";
    if ($user->can('update', $activeProfile)) {
        echo "YES\n";
    } else {
        echo "NO\n";
    }
    
    // Test direct policy check
    $policy = new \App\Policies\ProfilePolicy();
    echo "Direct policy check: ";
    if ($policy->update($user, $activeProfile)) {
        echo "ALLOWED\n";
    } else {
        echo "DENIED\n";
    }
    
    // Check IDs
    echo "\nUser ID: " . $user->id . "\n";
    echo "Profile user_id: " . $activeProfile->user_id . "\n";
    echo "IDs match: " . ($user->id === $activeProfile->user_id ? "YES" : "NO") . "\n";
    echo "IDs equal (==): " . ($user->id == $activeProfile->user_id ? "YES" : "NO") . "\n";
}

// Check if policy is registered
echo "\n=== Policy Registration ===\n";
$policies = app('Illuminate\Contracts\Auth\Access\Gate')->policies();
echo "Registered policies:\n";
foreach ($policies as $model => $policy) {
    echo "- $model => $policy\n";
}

// Check middleware
echo "\n=== Middleware Check ===\n";
$route = app('router')->getRoutes()->getByName('profiles.edit');
if ($route) {
    echo "Route middleware: " . implode(', ', $route->middleware()) . "\n";
} else {
    echo "Route not found\n";
}