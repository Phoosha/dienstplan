<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy {

    use HandlesAuthorization;

    public function before(User $user, $ability) {
        switch ($ability) {
            case 'promote':
            case 'delete':
            case 'resetAuthless':
            case 'viewApiToken':
            case 'resetApiToken':
                return null;
        }

        if ($user->is_admin)
            return true;
        elseif ($user->trashed())
            return false;
    }

    public function administrate(User $asUser) {
        return false;
    }

    public function edit(User $asUser, User $user) {
        return $asUser->is($user);
    }

    public function reset(User $asUser, User $user) {
        return $asUser->can('edit', $user);
    }

    public function resetAuthless(User $asUser, User $user) {
        return $asUser->can('reset', $user)
            && $asUser->isNot($user);
    }

    public function update(User $asUser, User $user) {
        return $asUser->can('edit', $user)
            && ( ! $user->wasChanged('is_admin') || $asUser->can('promote', $user) )
            && ( ! $user->wasChanged('login') || $asUser->can('changeLogin', $user));
    }

    public function promote(User $asUser, User $user) {
        // prevent admins from (accidentally) demoting themselves
        if ($asUser->is_admin && $asUser->isNot($user))
            return true;
        else
            return false;
    }

    public function changeLogin(User $asUser, User $user) {
        return $asUser->is($user);
    }

    public function delete(User $asUser, User $user) {
       if ($asUser->is_admin && $asUser->isNot($user))
           return true;
       else
           return false;
    }

    public function viewApiToken(User $asUser, User $user) {
        return $asUser->is($user);
    }

    public function resetApiToken(User $asUser, User $user) {
        return $asUser->is($user);
    }

}
