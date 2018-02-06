<?php

namespace App\Notifications;

use App\User;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CompleteRegistration extends Notification {

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param  string $token
     * @return void
     */
    public function __construct($token) {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  User $notifiable
     * @return array
     */
    public function via($notifiable) {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  User $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable) {
        $appName = config('app.name');
        return (new MailMessage)
            ->subject("Registrierung bei {$appName}")
            ->greeting("Herzlich willkommen {$notifiable->first_name}!")
            ->line("Du bekommst diese Nachricht, weil für dich ein Account bei '{$appName}' angelegt wurde.")
            ->line("Dein Nutzername lautet: {$notifiable->login}")
            ->action('Passwort setzen', url("register?register_token={$this->token}"))
            ->line('Wenn du das Passwort gesetzt hast, kannst du dich in Zukunft mit obigem Nutzernamen und dem Passwort anmelden.')
            ->line('Viel Spaß!');
    }

}
