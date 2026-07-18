<?php

namespace App\Providers;

use App\Utils\MailUtils;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        VerifyEmail::toMailUsing(
            function (object $notifiable, string $url): MailMessage {

                return (new MailMessage)
                ->subject('Confirmation of your email.')
                ->greeting("Hello, {$notifiable->first_name}!")
                ->line('Thank you for registering with Easy ERP.')
                ->line('To use our service you need to complete registration with email confirmation.')
                ->action('Confirm email', $url)
                ->line(
                    'The confirmation period expires after '
                    . config('auth.verification.expire', 60) . ' minute')
                ->line('If you did not create this account, then do not take any action.')
                ->salutation('Easy ERP System')
                ->withSymfonyMessage([MailUtils::class, 'attachLogo', ]);
            });

    }
}
