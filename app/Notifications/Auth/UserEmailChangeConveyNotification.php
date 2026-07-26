<?php

namespace App\Notifications\Auth;

use App\Utils\MailUtils;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserEmailChangeConveyNotification extends Notification implements ShouldQueue
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
            ->subject('Change mail')
            ->greeting("Hello, " . $notifiable->first_name . " " . $notifiable->middle_name)
            ->line("You sent a request to change your email to " . $notifiable->pending_email)
            ->line('You can confirm this at pending_email')
            ->salutation('Easy ERP system')
            ->withSymfonyMessage([MailUtils::class, 'attachLogo']);
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
