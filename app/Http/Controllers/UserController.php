<?php

namespace App\Http\Controllers;


use App\User;
use Auth;
use Illuminate\Foundation\Auth\ResetsPasswords;

class UserController extends Controller {

    use ResetsPasswords;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('throttle:3,5')->only('reset');
    }

    public function edit(User $user) {
        $this->authorize('edit', $user);

        return view('users.edit', compact('user'));
    }

    public function update(User $user) {
        $this->authorize('update', $user);

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

        $user->save();

        return redirect(url('users', $user->id))->with('status', 'Nutzerdaten wurden aktualisiert');
    }

    public function reset(User $user) {
        $this->authorize('reset', $user);

        // check if reauthentication of the logged in user is required
        $authNeeded = Auth::user()->cannot('resetAuthless', $user);

        $request = $this->validate(request(), [
            'password' => $authNeeded ? 'required|string' : '',
            'new-password' => $this->rules()['password'],
        ]);

        // reauthenticate the user if necessary using password
        if ($authNeeded && ! $this->reauthenticate($request['password']))
            return back()->withErrors(['password' => 'Passwort ist falsch']);

        // resetPassword logs $user in so we use a hack to revert to $authUser logged in
        $authUser = Auth::user();
        $this->resetPassword($user, $request['new-password']);
        $this->guard()->login($authUser);

        return redirect(url('users', $user->id))->with('password-status', 'Passwort wurde geändert');
    }

    protected function reauthenticate($password) {
        $credentials = [
            'id' => Auth::user()->id,
            'password' => $password,
        ];

        return Auth::guard()->validate($credentials);
    }

    public function destroy(User $user) {
        $this->authorize('delete', $user);
        $user->delete();

        return redirect('/'); // FIXME
    }

}