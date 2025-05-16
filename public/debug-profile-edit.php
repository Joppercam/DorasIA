<?php
require_once __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Gate;
use App\Policies\ProfilePolicy;

$app->instance('path.config', __DIR__.'/../config');
$app->instance('path.storage', __DIR__.'/../storage');
$app->instance('path.database', __DIR__.'/../database');
$app->instance('path.resources', __DIR__.'/../resources');
$app->instance('path.bootstrap', __DIR__.'/../bootstrap');
$app->instance('path.public', __DIR__);

// Boot the application
$app->boot();

header('Content-Type: application/json');

// Get user ID 1
$user = User::find(1);
if (!$user) {
    echo json_encode(['error' => 'User with ID 1 not found']);
    exit;
}

$responseData = [
    'user' => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
    ],
    'profiles' => [],
    'active_profile' => null,
    'policy_resolved' => false,
    'policy_check' => []
];

// Register the policy manually
Gate::policy(Profile::class, ProfilePolicy::class);

// Get all profiles for the user
$profiles = $user->profiles;
foreach ($profiles as $profile) {
    $profileData = [
        'id' => $profile->id,
        'name' => $profile->name,
        'is_child' => $profile->is_child,
        'user_id' => $profile->user_id,
        'can_update' => false
    ];
    
    // Direct policy check
    $policy = new ProfilePolicy();
    $profileData['direct_policy_check'] = $policy->update($user, $profile);
    $profileData['user_owns_profile'] = $user->id === $profile->user_id;
    
    $responseData['profiles'][] = $profileData;
}

// Get active profile
$activeProfile = $user->getActiveProfile();
if ($activeProfile) {
    $responseData['active_profile'] = [
        'id' => $activeProfile->id,
        'name' => $activeProfile->name,
        'user_id' => $activeProfile->user_id,
        'user_owns_profile' => $user->id === $activeProfile->user_id,
        'expected_route' => "/profiles/{$activeProfile->id}/edit"
    ];
    
    // Direct policy check
    $policy = new ProfilePolicy();
    $responseData['active_profile']['direct_policy_check'] = $policy->update($user, $activeProfile);
}

echo json_encode($responseData, JSON_PRETTY_PRINT);