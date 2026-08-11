<?php

namespace App\Notifications\Auth;

use App\Utils\MailUtils;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserEmailChangeVerifyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly string $newEmail,
        public readonly string $fullName,
        public readonly string $verificationUrl
    ){}

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
            ->subject('Confirm your new email')
            ->greeting("Hello, " . $this->fullName)
            ->line("You sent a request to change your email to " . $this->newEmail)
            ->line("You can confirm it immediately by clicking the button below")
            ->action('Confirm ', $this->verificationUrl)
            ->salutation('Easy ERP system')
            ->withSymfonyMessage([MailUtils::class, 'attachLogo']);

    }

}
