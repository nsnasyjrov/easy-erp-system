<?php

namespace App\Notifications\Auth;

use App\Models\User;
use App\Utils\MailUtils;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class UserEmailChangeVerifyNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $user
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
        $url = URL::temporarySignedRoute(
            'email.change.verify', now()->addMinutes(60),
            ['id' => $this->user,
             'hash' => sha1($this->user->pending_email)]);

        return (new MailMessage)
            ->subject('Изменение почты')
            ->greeting("Привет, " . $this->user->first_name . $this->user->middle_name)
            ->line("Ты отправил запрос на изменение почты на " . $this->user->pending_email)
            ->line("Ты можешь подтвердить ее сразу нажав кнопку снизу")
            ->action('Подтвердить ', $url)
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
