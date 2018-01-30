<?php

namespace App\Policies;

use App\Post;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy {

    use HandlesAuthorization;

    public function before(User $user, $ability) {
        if ($user->is_admin)
            return true;
        elseif ($user->trashed())
            return false;
    }

    public function edit(User $user) {
        return $user->can('store', Post::class)
            || $user->can('delete', Post::class);
    }

    public function store(User $user) {
        return false;
    }

    public function delete(User $user) {
        return false;
    }

}
