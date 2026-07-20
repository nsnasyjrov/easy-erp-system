<?php

namespace App\Listeners\Auth;

use App\Events\PasswordResetRequested;
use App\Notifications\Auth\UserForgotPasswordNotification;

class SendResetPasswordMessage
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {}

    /**
     * Handle the event.
     */
    public function handle(PasswordResetRequested $event): void
    {
        $event->user->notify(new UserForgotPasswordNotification($event->token));
    }
}
