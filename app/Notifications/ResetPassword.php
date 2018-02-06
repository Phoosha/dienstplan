<?php

namespace App\Notifications;

use App\User;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification {

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  User  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  User  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Passwort zurücksetzen')
            ->greeting("Hallo {$notifiable->first_name}!")
            ->line('Du bekommst diese Nachricht, weil jemand eine Anfrage gestellt hat dein Passwort zurückzusetzen.')
            ->line("Zur Erinnerung dein Nutzername lautet: {$notifiable->login}")
            ->action('Passwort zurücksetzen', url('password/reset', $this->token))
            ->line('Solltest nicht du diese Anfrage gestellt haben, kannst du diese Nachricht ignorieren.');
    }

}
