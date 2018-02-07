<?php

namespace App\Events;

use App\Duty;
use App\User;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Functionality common to all events about duties.
 *
 * @package App\Events
 */
abstract class DutyEvent {

    use Dispatchable, SerializesModels;

    public $duty;
    public $initiator;
    protected $original_sequence;

    /**
     * Create a new event instance for an operation on a <code>$duty</code>
     * caused by an <code>$initiator</code>.
     *
     * @param \App\Duty $duty
     * @param \App\User $initiator
     */
    public function __construct(Duty $duty, User $initiator) {
        $this->duty              = $duty;
        $this->initiator         = $initiator;
        $this->original_sequence = $duty->sequence;
    }

    /**
     * Returns whether the event was initiated by the current owner of this
     * events duty.
     *
     * @return bool
     */
    public function isSelfInitiated() {
        return $this->duty->user->is($this->initiator);
    }

    /**
     * Returns when to release the notification about this event.
     *
     * @return \Carbon\Carbon
     */
    public function getNotificationRelease(): Carbon {
        return $this->duty->updated_at
            ->copy()->add(
                config('dienstplan.duty_notification_delay')
            );
    }

    /**
     * Returns the current sequence number of the duty associated with this event.
     *
     * @return int
     */
    protected function getSequence(): int {
        return $this->duty->sequence;
    }

    /**
     * Returns the sequence number of the original duty used to create this event.
     *
     * @return int
     */
    protected function getOriginalSequence(): int {
        return $this->original_sequence;
    }

    /**
     * Returns whether this event may be coalesced with other events due to queuing.
     *
     * @return bool
     */
    public abstract function isCoalescable(): bool;

    /**
     * Returns whether this event perished.
     *
     * If an event perished, its associated duty must be considered changed in
     * such a way that it does not anymore represent a valid state after the event.
     *
     * @return bool
     */
    public function hasPerished(): bool {
        return $this->duty->trashed();
    }

}
