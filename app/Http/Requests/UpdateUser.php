<?php

namespace App\Http\Requests;

use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUser extends StoreUser {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $user = User::find($this->route('id'));

        return Auth::user()->can('edit', $user);
    }

    /**
     * Updates an existing <code>User</code> with <code>$attrs</code>.
     *
     * @param $attrs
     * @return User
     */
    protected function buildUser(&$attrs) {
       return User::find($this->route('id'))->fill($attrs);
    }

}
