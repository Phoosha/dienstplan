<?php

namespace App\Events;

use Carbon\Carbon;

/**
 * Event for when a new duty was created.
 *
 * Coalesces with other events and does not perish unless the duty is deleted.
 *
 * @package App\Events
 */
class DutyCreated extends DutyEvent {

    public function getNotificationRelease(): Carbon {
        return $this->duty->updated_at->copy()->addMinutes(3);
    }

    public function isCoalescable(): bool {
        return true;
    }

}
