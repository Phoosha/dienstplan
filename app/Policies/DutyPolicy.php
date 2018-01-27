<?php

namespace App\Policies;

use App\CalendarMonth;
use App\Shift;
use App\User;
use App\Duty;
use Carbon\Carbon;
use Illuminate\Auth\Access\HandlesAuthorization;
use InvalidArgumentException;

class DutyPolicy {

    use HandlesAuthorization;

    protected $store_start;
    protected $store_end;
    protected $view_start;

    public function __construct() {
        $this->store_start = self::getStoreStart();
        $this->store_end   = self::getStoreEnd();
        $this->view_start  = self::getViewStart();
    }

    /**
     * Determines starting from when duties may be stored.
     *
     * @return Carbon
     */
    public static function getStoreStart() {
        $now_shift = new Shift(now());
        return $now_shift->start;
    }

    /**
     * Determines starting up to when duties may be stored in advance.
     *
     * @return Carbon
     */
    public static function getStoreEnd() {
        $now_shift = new Shift(now());
        return $now_shift->end->add(config('dienstplan.store_threshold'));
    }

    /**
     * Determines starting from when past duties may be viewed.
     *
     * @return Carbon
     */
    public static function getViewStart() {
        return now()
            ->subMonths(config('dienstplan.view_past_months'))
            ->firstOfMonth();
    }

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
        return $duty->end >= $this->view_start;
    }

    /**
     * Determine whether the user can create duties.
     *
     * @param  \App\User $user
     * @param  \App\Duty $duty
     * @return mixed
     */
    public function create(User $user, Duty $duty) {
        return $duty->end <= $this->store_end && $duty->start >= $this->store_start;
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
