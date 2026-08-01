<?php

namespace App\Listeners\Auth;

use App\Events\Auth\PasswordChangedEvent;
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
    public function handle(PasswordChangedEvent $event): void
    {
        $event->user->notify(new UserPasswordChangedNotification());
    }
}
