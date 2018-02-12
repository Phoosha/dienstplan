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
use Log;
use Throwable;

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
     * @return MailMessage
     */
    public function toMail($notifiable) {
        $attach = false;
        $class  = get_class($this->event);
        switch ($class) {
            case DutyCreated::class:
                $view    = 'emails.duty.created';
                $subject = 'Dienst wurde eingetragen';
                $attach  = true;
                break;
            case DutyReassigned::class:
                $view    = 'emails.duty.reassigned';
                $subject = 'Dienst wurde übereignet';
                $attach  = true;
                break;
            case DutyUpdated::class:
                $view    = 'emails.duty.updated';
                $subject = 'Dienst wurde aktualisiert';
                $attach  = true;
                break;
            case DutyDeleted::class:
                $view    = 'emails.duty.deleted';
                $subject = 'Dienst wurde gelöscht';
                break;
            default:
                throw new InvalidArgumentException("Unknown DutyEvent of class: {$class}");
        }

        $mail = ( new MailMessage )
            ->subject($subject)
            ->markdown($view, $this->getData($notifiable));

        if ($attach) {
            $this->attachDuty($mail);
        }

        return $mail;
    }

    /**
     * Get the data for the view.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    protected function getData($notifiable): array {
        return [
            'event' => $this->event,
            'rcpt' => $notifiable instanceof User ? $notifiable : null,
        ];
    }

    /**
     * Attach the duty as iCalendar to the mail.
     *
     * @param MailMessage $mail
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function attachDuty(MailMessage $mail): MailMessage {
        $duty = $this->event->duty;

        try {
            return $mail->attachData(
                view('api.duties', [
                    'duty' => $duty,
                    'method' => 'PUBLISH',
                    'cal_name' => config('app.name') . ' ' . $duty->user->getFullName(),
                ])->render(),
                "Dienst-{$duty->start->toDateString()}-v{$duty->sequence}.ics",
                [ 'mime' => 'text/calendar; charset=utf-8; method="PUBLISH"' ]
            );
        } catch (Throwable $e) {
            Log::error('Could not generate iCalendar from duty', [ $e, $duty ]);
        }

        return $mail;
    }

}