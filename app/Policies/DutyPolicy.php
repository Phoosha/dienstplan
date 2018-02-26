<?php

namespace App\Policies;

use App\Duty;
use App\User;
use App\ViewModels\Shift;
use Carbon\Carbon;
use Illuminate\Auth\Access\HandlesAuthorization;

class DutyPolicy {

    use HandlesAuthorization;

    /**
     * Runs before any <code>$ability</code> and may overwrite its
     * result for <code>$user</code>.
     *
     * @param User $user
     * @param $ability
     * @return bool|null
     */
    public function before(User $user, $ability) {
        if ($user->is_admin)
            return true;
        elseif ($user->trashed())
            return false;
    }

    /**
     * Determine whether the <code>$user</code> can view the duty.
     *
     * @param  \App\User $user
     * @param  \App\Duty $duty
     * @return bool
     */
    public function view(User $user, Duty $duty) {
        return $duty->end >= self::view_start($user);
    }

    /**
     * Determine whether the <code>$user</code> could have permission
     * to save some duty built from <code>$duty</code>.
     *
     * @param  \App\User $user
     * @param  \App\Duty $duty
     * @return bool
     */
    public function create(User $user, Duty $duty) {
        return $user->can('store', Duty::class)
            && $duty->start >= self::store_start($user)
            && $duty->end   <= self::store_end($user);
    }

    /**
     * Determine whether the <code>$user</code> can store any duty.
     *
     * @param \App\User $user
     * @return bool
     */
    public function store(User $user) {
        return true;
    }

    /**
     * Determine whether the <code>$user</code> can save <code>$duty</code>.
     *
     * @param  \App\User $user
     * @param  \App\Duty $duty
     * @return bool
     */
    public function save(User $user, Duty $duty) {
        return $user->can('create', $duty)
            && ( $duty->type !== Duty::SERVICE || $user->can('service', Duty::class) )
            && ( $duty->user->is($user) || $user->can('impersonate', Duty::class) );
    }

    /**
     * Determine whether the <code>$user</code> can store duties for another user.
     *
     * @param  \App\User $user
     * @return bool
     */
    public function impersonate(User $user) {
        return false;
    }

    /**
     * Determine whether the <code>$user</code> can save a duty of SERVICE type.
     *
     * @param \App\User $user
     * @return bool
     */
    public function service(User $user) {
        return false;
    }

    /**
     * Determine whether the <code>$user</code> can edit the <code>$duty</code> in some way.
     *
     * @param  \App\User $user
     * @param  \App\Duty $duty
     * @return bool
     */
    public function edit(User $user, Duty $duty) {
        return $user->can('update', $duty)
            || $user->can('delete', $duty);
    }

    /**
     * Determine whether the <code>$user</code> can update the <code>$duty</code> with any change at all.
     *
     * @param  \App\User $user
     * @param  \App\Duty $duty
     * @return bool
     */
    public function update(User $user, Duty $duty) {
        return $duty->start >= self::update_start($user, $duty)
            && ( $duty->user->is($user) || $user->can('impersonate', Duty::class) );
    }

    /**
     * Determine whether the <code>$user</code> can delete the <code>$duty</code>.
     *
     * @param  \App\User $user
     * @param  \App\Duty $duty
     * @return mixed
     */
    public function delete(User $user, Duty $duty) {
        return $this->update($user, $duty);
    }

    /**
     * Determines starting from when duties may be stored.
     *
     * @param \App\User $user
     * @return \Carbon\Carbon
     */
    public static function store_start(User $user) {
        if ($user->is_admin)
            return Carbon::instance(config('dienstplan.min_date'));

        $now_shift = new Shift(now());
        return $now_shift->start
            ->max(
                Carbon::instance(config('dienstplan.min_date'))
            );
    }

    /**
     * Determines up to when duties may be stored in advance.
     *
     * @param \App\User $user
     * @return \Carbon\Carbon
     */
    public static function store_end(User $user) {
        if ($user->is_admin)
            return Carbon::instance(config('dienstplan.max_date'));

        $now_shift = new Shift(now());
        return $now_shift->end
            ->add(config('dienstplan.duty.store_future'))
            ->min(
                Carbon::instance(config('dienstplan.max_date'))
            );
    }

    /**
     * Determines starting from when past duties may be viewed.
     *
     * @param \App\User $user
     * @return \Carbon\Carbon
     */
    public static function view_start(User $user) {
        if ($user->is_admin)
            return Carbon::instance(config('dienstplan.min_date'));

        return now()
            ->sub(config('dienstplan.duty.view_past'))
            ->firstOfMonth()
            ->max(
                Carbon::instance(config('dienstplan.min_date'))
            );
    }

    /**
     * Determines starting from when <code>$duty</code> may be updated.
     *
     * @param \App\User $user
     * @param \App\Duty $duty
     *
     * @return \Carbon\Carbon
     */
    public static function update_start(User $user, Duty $duty) {
        $grace_until = now()->sub(config('dienstplan.duty.modify_grace'));
        $from        = self::store_start($user);

        // allow to edit a duty if it was recently created
        if ($duty->created_at < $grace_until)
            $from->add(config('dienstplan.duty.modify'));
        else
            $from = $duty->created_at->copy();

        return $from->min(
            Carbon::instance(config('dienstplan.max_date'))
        );
    }

}
