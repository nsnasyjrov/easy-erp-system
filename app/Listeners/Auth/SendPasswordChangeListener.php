<?php

namespace App\Listeners\Auth;

use App\Events\Auth\PasswordChanged;
use App\Notifications\Auth\UserPasswordChangedNotification;

class SendPasswordChangeListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PasswordChanged $event): void
    {
        $event->user->notify(new UserPasswordChangedNotification());
    }
}
