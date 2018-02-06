<?php

namespace App\Events;

/**
 * Event for when a duty is updated without reassigning its owner.
 *
 * Coalesces with other events and perishes with any further change to the
 * associated duty.
 *
 * @package App\Events
 */
class DutyUpdated extends DutyEvent {

    use PerishesBySequence;

    public function isCoalescable(): bool {
        return true;
    }

}
