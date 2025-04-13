<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register routes with web middleware first to handle session state
        Broadcast::routes(['middleware' => ['web', 'auth:web']]);

        require base_path('routes/channels.php');
    }
}
