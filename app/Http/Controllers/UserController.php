<?php

namespace App\Http\Controllers;


use App\Http\Requests\UpdateUser;
use App\User;
use Auth;
use Illuminate\Foundation\Auth\ResetsPasswords;

class UserController extends Controller {

    use ResetsPasswords;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('throttle:3,5')->only('reset');
    }

    public function view() {
        $this->authorize('administrate', User::class);

        $users = User::ordering()->get();
        $trashed = User::onlyTrashed()->ordering()->get();

        return view('admin.users', compact('users', 'trashed'));
    }

    public function editMe() {
        return $this->edit(Auth::user());
    }

    public function edit(User $user) {
        $this->authorize('edit', $user);

        return view('users.edit', compact('user'));
    }

    public function update(UpdateUser $request) {
        $user = $request->getUser();
        $this->authorize('update', $user);
        $user->save();

        return back()->with('status', 'Nutzerdaten wurden aktualisiert');
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

        return back()->with('password-status', 'Passwort wurde geändert');
    }

    public function resetToken(User $user) {
        $this->authorize('resetApiToken', $user);

        $user->cycleApiToken();

        return back()->with('api-status', 'Adresse und Zugriffscode wurden zurückgesetzt');
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

        return back();
    }

}