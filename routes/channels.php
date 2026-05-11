<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels – BIMA Multi-Agent System
|--------------------------------------------------------------------------
| chat-session.{id}  → Public channel (events go there via ShouldBroadcastNow)
| user.{id}          → Per-user notifications (e.g. document processed)
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Public channels — no auth required (using Channel, not PrivateChannel)
// Authorization is enforced at the HTTP route level via auth middleware.
Broadcast::channel('chat-session.{sessionId}', function () {
    return true;
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
