<?php

namespace App\Events;

use App\Duty;
use App\User;

/**
 * Event for when a duty is updated reassigning its owner.
 *
 * Coalesces with other events and perishes with any further change to the
 * associated duty.
 *
 * @package App\Events
 */
class DutyReassigned extends DutyEvent {

    use PerishesBySequence;

    /**
     * @var \App\User Original owner of the duty before the reassignment
     */
    public $original_owner;

    /**
     * Create a new event instance for the update of the previously to
     * to <code>$originalOwner</code> belonging <code>$duty</duty> caused
     * by <code>$initiator</code>.
     *
     * @param \App\Duty $duty
     * @param \App\User $initiator
     * @param \App\User $originalOwner
     */
    public function __construct(Duty $duty, User $initiator, User $originalOwner) {
        parent::__construct($duty, $initiator);
        $this->original_owner = $originalOwner;
    }

    public function isCoalescable(): bool {
        return true;
    }

}
