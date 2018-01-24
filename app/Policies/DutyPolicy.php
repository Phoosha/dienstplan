<?php

namespace App\Policies;

use App\CalendarMonth;
use App\Shift;
use App\User;
use App\Duty;
use Illuminate\Auth\Access\HandlesAuthorization;

class DutyPolicy {

    use HandlesAuthorization;

    public function before(User $user, $ability) {
        if ($user->is_admin)
            return true;
        elseif ($user->trashed())
            return false;
    }

    /**
     * Determine whether the user can view the duty.
     *
     * @param  \App\User $user
     * @param  \App\Duty $duty
     * @return mixed
     */
    public function view(User $user, Duty $duty) {
        $threshold_dt = now()
            ->subMonths(config('dienstplan.view_past_months'))
            ->firstOfMonth();

        return $duty->end >= $threshold_dt;
    }

    /**
     * Determine whether the user can create duties.
     *
     * @param  \App\User $user
     * @param  \App\Duty $duty
     * @return mixed
     */
    public function create(User $user, Duty $duty) {
        $threshold_dt = now()->add(config('dienstplan.store_threshold'));
        $now_shift    = new Shift(now());

        return $duty->end <= $threshold_dt && $duty->start >= $now_shift->start;
    }

    /**
     * Determine whether the user can store the duty.
     *
     * @param  \App\User $user
     * @param  \App\Duty $duty
     * @return mixed
     */
    public function store(User $user, Duty $duty) {
        return $this->create($user, $duty) && $duty->type !== Duty::SERVICE;
    }

    /**
     * Determine whether the user can store a duty for another user.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function impersonate(User $user) {
        return false;
    }

    /**
     * Determine whether the user can edit the duty.
     *
     * @param  \App\User $user
     * @param  \App\Duty $duty
     * @return mixed
     */
    public function edit(User $user, Duty $duty) {
        return $this->update($user, $duty) || $this->delete($user, $duty);
    }

    /**
     * Determine whether the user can update the duty.
     *
     * @param  \App\User $user
     * @param  \App\Duty $duty
     * @return mixed
     */
    public function update(User $user, Duty $duty) {
        $threshold_dt = now()->add(config('dienstplan.modify_threshold'));

        return $duty->start >= $threshold_dt && $duty->user->is($user);
    }

    /**
     * Determine whether the user can delete the duty.
     *
     * @param  \App\User $user
     * @param  \App\Duty $duty
     * @return mixed
     */
    public function delete(User $user, Duty $duty) {
        return $this->update($user, $duty);
    }

}
