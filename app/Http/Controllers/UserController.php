<?php

namespace App\Http\Controllers;


use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;

class UserController extends Controller {

    use ResetsPasswords;

    public function __construct() {
        $this->middleware('auth');
    }

    public function edit(User $user) {
        $this->authorize('edit', $user);

        return view('users.edit', compact('user'));
    }

    public function update(User $user) {
        $request = $this->validate(request(), [
            'first_name' => 'required|alpha_dash|max:35',
            'last_name' => 'required|alpha_dash|max:35',
            'login' => "sometimes|alpha_num|max:35|unique:users,login,{$user->id}",
            'email' => 'required|email|max:100',
            'phone' => [ 'present', 'max:35', 'regex:' . config('dienstplan.phone_regex') ],
            'is_admin' => 'sometimes|boolean'
        ]);

        $user = $user->fill($request);
        $user->is_admin = $request['is_admin'] ?? $user->is_admin;

        $this->authorize('update', $user);
        $user->save();

        return redirect(url('users', $user->id))->with('status', 'Nutzerdaten wurden aktualisiert');
    }

    public function reset(User $user) {
        $request = $this->validate(request(), [
            'password' => $this->rules()['password'],
        ]);

        $this->authorize('reset', $user);
        $this->resetPassword($user, $request['password']);

        return redirect(url('users', $user->id))->with('password-status', 'Passwort wurde geändert');
    }

    public function destroy(User $user) {
        $this->authorize('delete', $user);
        $user->delete();

        return redirect('/'); // FIXME
    }

}