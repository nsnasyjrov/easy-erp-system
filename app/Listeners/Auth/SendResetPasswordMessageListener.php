<?php

namespace App\Listeners\Auth;

use App\Events\Auth\PasswordResetRequestedEvent;
use App\Notifications\Auth\UserForgotPasswordNotification;

class SendResetPasswordMessageListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {}

    /**
     * Handle the event.
     */
    public function handle(PasswordResetRequestedEvent $event): void
    {
        $event->user->notify(new UserForgotPasswordNotification($event->token));
    }
}
