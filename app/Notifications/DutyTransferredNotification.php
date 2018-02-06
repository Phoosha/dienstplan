<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class DutyTransferredNotification extends DutyNotification {

    public function toMail($notifiable) {
        return ( new MailMessage() )
            ->subject('Dienst wurde übergeben')
            ->markdown('emails.duty.transferred', $this->getData($notifiable));
    }

}