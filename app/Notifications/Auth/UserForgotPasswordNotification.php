<?php

namespace App\Notifications\Auth;

use App\Utils\MailUtils;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

    class UserForgotPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private readonly string $token
    )
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = rtrim(config('app.frontend_url'), '/') . '/reset-password?' .
            http_build_query(['token' => $this->token,
                              'email' => $notifiable->email]);

        return (new MailMessage)
            ->subject('Forgot password')
            ->greeting("Hello, {$notifiable->fullName()}")
            ->line('We have sent you a link to reset your password..')
            ->action("Restore access", $url)
            ->line("If you are not the one trying to restore access, then do nothing.")
            ->salutation('Easy ERP system')
            ->withSymfonyMessage([MailUtils::class, 'attachLogo',]);

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
