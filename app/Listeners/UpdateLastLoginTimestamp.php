<?php


namespace App\Listeners;

use App\Events\UserLoggedIn;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateLastLoginTimestamp implements ShouldQueue
{
    public function handle(UserLoggedIn $event)
    {
        $event->user->update([
            'last_login_at' => now(),
        ]);
    }
}
