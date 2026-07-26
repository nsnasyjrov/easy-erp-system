<?php

namespace App\Notifications\Auth;

use App\Utils\MailUtils;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserPasswordChangedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
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
        return (new MailMessage)
            ->subject('Password changed')
            ->greeting("Hello, {$notifiable->fullName()}")
            ->line('Your passport has been successfully changed')
            ->line("If you are not the one who changed the password, please contact support immediately.")
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
