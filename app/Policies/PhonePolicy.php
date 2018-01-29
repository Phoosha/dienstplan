<?php

namespace App\Policies;

use App\Phone;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PhonePolicy {

    use HandlesAuthorization;

    public function before(User $user, $ability) {
        if ($user->is_admin)
            return true;
        elseif ($user->trashed())
            return false;
    }

    public function edit(User $user) {
        return $user->can('store', Phone::class)
            || $user->can('delete', Phone::class);
    }

    public function store(User $user) {
        return false;
    }

    public function delete(User $user) {
        return false;
    }

}
