<?php

namespace App\Jobs;

use App\Events\DutyEvent;
use App\Events\DutyReassigned;
use App\Listeners\DispatchDutyNotification;
use App\Notifications\DutyNotification;
use App\Notifications\DutyTransferredNotification;
use Cache;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendDutyNotification implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    protected $event;

    /**
     * Create a new job that sends a notification from <code>$event</code>.
     *
     * @param \App\Events\DutyEvent $event
     */
    public function __construct(DutyEvent $event) {
        $this->event = $event;
    }

    /**
     * Send a notification with the most recent data available..
     *
     * @return void
     */
    public function handle() {
        DispatchDutyNotification::sentNotificationFor($this->event->duty);
        $this->event->duty->refresh();

        if ($this->event->isCoalescable() && $this->event->hasPerished()) {
            $event = DispatchDutyNotification::getLastEvent($this->event->duty);
            if (empty($event))
                return;
            else
                $this->event = $event;
        }

        $this->event->duty->user
            ->notifyNow(new DutyNotification($this->event));

        if ($this->event instanceof DutyReassigned) {
            $this->event->original_owner
                ->notifyNow(new DutyTransferredNotification($this->event));
        }

    }

    /**
     * Cleanup when the event permanently fails.
     *
     * @param null $exception
     */
    public function failed($exception = null) {
        DispatchDutyNotification::sentNotificationFor($this->event->duty);
    }

}
