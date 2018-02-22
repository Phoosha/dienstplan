<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy {

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
        switch ($ability) {
            case 'promote':
            case 'delete':
            case 'forceDelete':
            case 'resetPasswordless':
            case 'viewApiToken':
            case 'resetApiToken':
                return null;
        }

        if ($user->is_admin)
            return true;
        elseif ($user->trashed())
            return false;
    }

    /**
     * <code>$asUser</code> can view all users to perform administrative tasks..
     *
     * @param \App\User $asUser
     *
     * @return bool
     */
    public function administrate(User $asUser) {
        return false;
    }

    /**
     * <code>$asUser</code> can edit <code>$user</code> in some way.
     *
     * @param \App\User $asUser
     * @param \App\User $user
     *
     * @return bool
     */
    public function edit(User $asUser, User $user) {
        return $asUser->is($user);
    }

    /**
     * <code>$asUser</code> can store the updated <code>$user</code> with its
     * modifications.
     *
     * @param \App\User $asUser
     * @param \App\User $user
     *
     * @return bool
     */
    public function update(User $asUser, User $user) {
        return $asUser->can('edit', $user)
            && ( ! $user->isDirty('is_admin') || $asUser->can('promote', $user) )
            && ( ! $user->isDirty('login') || $asUser->can('changeLogin', $user))
            && ( ! $user->isDirty('last_training') || $asUser->can('setLastTraining', $user));
    }

    /**
     * <code>$asUser</code> can reset the password of <code>$user</code>.
     *
     * @param \App\User $asUser
     * @param \App\User $user
     *
     * @return bool
     */
    public function reset(User $asUser, User $user) {
        return $asUser->can('edit', $user);
    }

    /**
     * <code>$asUser</code> can reset the password of <code>$user</code> without
     * re-authentication with the current password of <code>$asUser</code>.
     *
     * @param \App\User $asUser
     * @param \App\User $user
     *
     * @return bool
     */
    public function resetPasswordless(User $asUser, User $user) {
        return ( $asUser->can('reset', $user) && $asUser->isNot($user) )
            || ( isset($user->register_token) && $asUser->is($user) );
    }

    /**
     * <code>$asUser</code> can register new users.
     *
     * @param \App\User $asUser
     *
     * @return bool
     */
    public function create(User $asUser) {
        return false;
    }

    /**
     * <code>$asUser</code> can save the new <code>$user</code>.
     *
     * @param \App\User $asUser
     * @param \App\User $user
     *
     * @return bool
     */
    public function save(User $asUser, User $user) {
        return $asUser->can('create', User::class);
    }

    /**
     * <code>$asUser</code> can change the permission level of <code>$user</code>.
     *
     * @param \App\User $asUser
     * @param \App\User $user
     *
     * @return bool
     */
    public function promote(User $asUser, User $user) {
        // prevent admins from (accidentally) demoting themselves
        return $asUser->is_admin && $asUser->isNot($user);
    }

    /**
     * <code>$asUser</code> can change the login field of <code>$user</code>.
     *
     * @param \App\User $asUser
     * @param \App\User $user
     *
     * @return bool
     */
    public function changeLogin(User $asUser, User $user) {
        return $asUser->is($user);
    }

    /**
     * <code>$asUser</code> can set the last_training field of <code>$user</code>.
     *
     * @param \App\User $asUser
     * @param \App\User $user
     *
     * @return bool
     */
    public function setLastTraining(User $asUser, User $user) {
        return false;
    }

    /**
     * <code>$asUser</code> can delete <code>$user</code>.
     *
     * @param \App\User $asUser
     * @param \App\User $user
     *
     * @return bool
     */
    public function delete(User $asUser, User $user) {
       return $asUser->is_admin && $asUser->isNot($user);
    }

    /**
     * <code>$asUser</code> can access the api_token field of <code>$user</code>.
     *
     * @param \App\User $asUser
     * @param \App\User $user
     *
     * @return bool
     */
    public function viewApiToken(User $asUser, User $user) {
        return $asUser->is($user);
    }

    /**
     * <code>$asUser</code> can reset the api_token field of <code>$user</code>.
     *
     * @param \App\User $asUser
     * @param \App\User $user
     *
     * @return bool
     */
    public function resetApiToken(User $asUser, User $user) {
        return $asUser->is($user);
    }

    /**
     * <code>$asUser</code> can irrevocably delete <code>$user</code>.
     *
     * @param \App\User $asUser
     * @param \App\User $user
     *
     * @return bool
     */
    public function forceDelete(User $asUser, User $user) {
        return $asUser->can('delete', $user);
    }

    /**
     * <code>$asUser</code> can un-delete a soft-deleted <code>$user</code>.
     *
     * @param \App\User $asUser
     * @param \App\User $user
     *
     * @return bool
     */
    public function restore(User $asUser, User $user) {
        return false;
    }

}
