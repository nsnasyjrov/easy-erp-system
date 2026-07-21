<?php

namespace App\Listeners\Auth;

use App\Notifications\Auth\UserRestoredPasswordNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAfterResetPasswordUserMessageListener
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
    public function handle(PasswordReset $event): void
    {
        $event->user->notify(new UserRestoredPasswordNotification);
    }
}
