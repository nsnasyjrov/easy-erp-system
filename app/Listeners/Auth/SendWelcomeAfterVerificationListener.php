<?php

namespace App\Listeners\Auth;

use App\Notifications\Auth\UserVerifiedWelcomeNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWelcomeAfterVerificationListener
{
    /**
     * Create the event listener.
     */


    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        $event->user->notify(new UserVerifiedWelcomeNotification);
    }
}
