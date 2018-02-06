<?php

namespace App\Notifications;

use App\Events\DutyCreated;
use App\Events\DutyDeleted;
use App\Events\DutyEvent;
use App\Events\DutyReassigned;
use App\Events\DutyUpdated;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;

class DutyNotification extends Notification {

    use Queueable;

    public $tries = 3;

    protected $event;

    /**
     * Create a duty notification from <code>$event</code>
     *
     * @param \App\Events\DutyEvent $event
     */
    public function __construct(DutyEvent $event) {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable) {
        return [ 'mail' ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable) {
        $class = get_class($this->event);
        switch ($class) {
            case DutyCreated::class:
                $view    = 'emails.duty.created';
                $subject = 'Dienst wurde eingetragen';
                break;
            case DutyReassigned::class:
                $view    = 'emails.duty.reassigned';
                $subject = 'Dienst wurde übereignet';
                break;
            case DutyUpdated::class:
                $view    = 'emails.duty.updated';
                $subject = 'Dienst wurde aktualisiert';
                break;
            case DutyDeleted::class:
                $view    = 'emails.duty.deleted';
                $subject = 'Dienst wurde gelöscht';
                break;
            default:
                throw new InvalidArgumentException("Unknown DutyEvent of class: {$class}");
        }

        return ( new MailMessage )
            ->subject($subject)
            ->markdown($view, $this->getData($notifiable));
    }

    /**
     * Get the data for the view.
     *
     * @param $notifiable
     *
     * @return array
     */
    protected function getData($notifiable): array {
        return [
            'event' => $this->event,
            'rcpt' => $notifiable instanceof User ? $notifiable : null,
        ];
    }

}