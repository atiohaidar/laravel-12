<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Private chat channels
Broadcast::channel('chat.{id}', function ($user, $id) {
    return true; // Temporarily allow all authenticated users for testing
});

// Presence channel for online status
Broadcast::presence('presence.chat', function ($user) {
    return $user ? ['id' => $user->id, 'name' => $user->name] : false;
});



