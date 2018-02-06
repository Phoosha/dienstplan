<?php

namespace App\Events;

/**
 * Event for when a duty is deleted.
 *
 * Does not coalesce with other events and does not perished unless the duty is restored.
 *
 * @package App\Events
 */
class DutyDeleted extends DutyEvent {

    public function isCoalescable(): bool {
        return false;
    }

    public function hasPerished(): bool {
        return ! $this->duty->trashed();
    }
}
