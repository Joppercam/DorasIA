<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

// Simular una petición HTTP
$user = User::first();
Auth::login($user);

echo "=== DEBUG 403 ERROR ===\n\n";
echo "User: " . $user->email . " (ID: " . $user->id . ")\n";

// Get active profile
$activeProfile = $user->getActiveProfile();
echo "Active Profile: " . ($activeProfile ? $activeProfile->name . " (ID: " . $activeProfile->id . ")" : "None") . "\n\n";

// Probar con diferentes IDs de perfil
$testProfiles = [
    1 => "Profile ID 1",
    2 => "Profile ID 2",
    3 => "Profile ID 3"
];

foreach ($testProfiles as $profileId => $description) {
    echo "=== Testing $description ===\n";
    
    $profile = Profile::find($profileId);
    if (!$profile) {
        echo "Profile not found\n\n";
        continue;
    }
    
    echo "Profile: " . $profile->name . " (ID: " . $profile->id . ")\n";
    echo "Profile User ID: " . $profile->user_id . "\n";
    echo "Current User ID: " . $user->id . "\n";
    
    // Test direct policy
    $policy = new \App\Policies\ProfilePolicy();
    echo "Direct Policy Check: " . ($policy->update($user, $profile) ? "ALLOWED" : "DENIED") . "\n";
    
    // Test Gate
    echo "Gate Check: " . (Gate::allows('update', $profile) ? "ALLOWED" : "DENIED") . "\n";
    
    // Test can method
    echo "Can Method: " . ($user->can('update', $profile) ? "ALLOWED" : "DENIED") . "\n";
    
    echo "\n";
}

// Check all profiles in the database
echo "=== All Profiles in Database ===\n";
$allProfiles = Profile::all();
foreach ($allProfiles as $profile) {
    echo "Profile ID: " . $profile->id . ", Name: " . $profile->name . ", User ID: " . $profile->user_id . "\n";
}

// Check middleware
echo "\n=== Middleware Test ===\n";
$request = \Illuminate\Http\Request::create('/profiles/2/edit', 'GET');
$request->setUserResolver(function () use ($user) {
    return $user;
});

try {
    $profile = Profile::find(2);
    if ($profile) {
        // Simulate the route parameter binding
        $request->route()->setParameter('profile', $profile);
        
        echo "Testing authorization for profile ID 2\n";
        echo "Profile user_id: " . $profile->user_id . "\n";
        echo "Current user id: " . $user->id . "\n";
        
        if (Gate::denies('update', $profile)) {
            echo "DENIED by Gate\n";
        } else {
            echo "ALLOWED by Gate\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}