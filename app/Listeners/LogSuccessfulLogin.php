<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\Notification;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        Notification::create([
            'description' => 'Utilisateur ' . $event->user->name . ' connecté avec succès.'
        ]);
    }
}
