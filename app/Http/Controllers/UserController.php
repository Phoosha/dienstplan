<?php

namespace App\Http\Controllers;


use App\Http\Requests\StoreUser;
use App\Http\Requests\UpdateUser;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Log;
use Throwable;

class UserController extends Controller {

    use ResetsPasswords;

    public function __construct() {
        /*
         * EVERYTHING goes through the regular WEB auth guard, except:
         *  register -> only REGISTER auth guard
         *  reset    -> both WEB + REGISTER auth guard
         */
        $this->middleware('auth')->except([ 'register', 'setPassword' ]);
        $this->middleware('auth:register')->only('register');
        $this->middleware('auth:web,register')->only('setPassword');

        /*
         * This is just being overly careful to prevent some Eve, who somehow
         * got into possession of Alice's session from easily brute-forcing her
         * current password and thereby resetting her password.
         * Additionally the register token is similarly protected from
         * brute-forcing for Alice's register token.
         */
        $this->middleware('throttle:6,5')->only('setPassword');
        $this->middleware('throttle:10,5')->only('register');
    }

    /**
     * Renders an overview of existing (active and trashed) users.
     *
     * @param bool $add whether to load a form to add new users
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function view(bool $add = false) {
        $this->authorize('administrate', User::class);

        $users = User::ordering()->get();
        $trashed = User::onlyTrashed()->ordering()->get();

        return view('admin.users.index', compact('users', 'trashed', 'add'));
    }

    /**
     * Renders a form to create new users.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create() {
        return $this->view(true);
    }

    /**
     * Renders a form to view and possibly edit the data of <code>$user</code>
     *
     * @param \App\User $user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(User $user) {
        $this->authorize('edit', $user);

        return view('users.edit', compact('user'));
    }

    /**
     * Calls {@see edit()} with the currently authenticated user.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function editMe() {
        return $this->edit(Auth::user());
    }

    /**
     * Handles a request to update the data of some user.
     *
     * @param \App\Http\Requests\UpdateUser $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateUser $request) {
        $user = $request->getUser();

        $this->authorize('update', $user);
        $user->save();

        return back()->with('status', 'Nutzerdaten wurden aktualisiert');
    }

    /**
     * Handles a request to register a new user.
     *
     * This involves generating a <code>register_token</code> that allows the
     * user to authenticate only with a registration form and to setup his
     * password once.
     *
     * @param \App\Http\Requests\StoreUser $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreUser $request) {
        $user = $request->getUser();
        $user->password = '';
        $user->register_token = str_random(60);

        $this->authorize('save', $user);
        $user->save();
        $user->sendRegisterNotification($user->register_token);

        return redirect('admin/users/create')->with('status', 'Neuer Nutzer wurde angelegt und wird per Mail benachrichtigt');
    }

    /**
     * Renders a form for a user to complete his registration and to set a
     * password.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function register(Request $request) {
        // logout a currently logged in user on the web instead of register guard
        Auth::guard('web')->logout();

        $user = Auth::user();
        $register_token = $request->get('register_token');

        $redirect = $this->checkRegistration($user);

        return $redirect ?? view('users.register', compact('user', 'register_token'));
    }

    /**
     * Handles a a request to set a new password for a user.
     *
     * @param \App\User $user
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function setPassword(User $user) {
        /*
         * We either are authenticated by the WEB or REGISTER guard when
         * reaching here. The latter needs some additional checking.
         */
        if (Auth::guard('register')->check()) {
            $redirect = $this->checkRegistration($user);

            if ($redirect)
                return $redirect;
        }

        $this->authorize('reset', $user);

        /*
         * Check if re-authentication of the logged in user is required. This
         * at least prevents someone, who got access to a (stale) session of
         * $user, from locking him out by resetting password and changing the
         * email address.
         */
        $auth_needed = Auth::user()->cannot('resetPasswordless', $user);

        $request = $this->validate(request(), [
            'password' => $auth_needed ? 'required|string' : '',
            'new-password' => $this->rules()['password'],
        ]);

        // re-authenticate the user if necessary using password
        if ($auth_needed && ! $this->reauthenticate($request['password']))
            return back()->withErrors(['password' => 'Passwort ist falsch']);

        /*
         * resetPassword logs $user in so we use a hack to keep the current
         * user, given by $authUser, logged into the application
         */
        $auth_user = Auth::user();
        $this->resetPassword($user, $request['new-password']);
        $this->guard()->login($auth_user);

        if (Auth::guard('register')->check())
            return redirect('/')->with('status', 'Passwort wurde gesetzt');

        return back()->with('password-status', 'Passwort wurde geändert');
    }

    /**
     * Handles a request to reset the <code>api_token</code> of a user.
     *
     * @param \App\User $user
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function resetToken(User $user) {
        $this->authorize('resetApiToken', $user);

        $user->cycleApiToken();

        return back()->with('api-status', 'Adresse und Zugriffscode wurden zurückgesetzt');
    }

    /**
     * Handles a request to delete a user.
     *
     * @param \App\User $user
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(User $user) {
        $this->authorize('delete', $user);
        $user->delete();

        return redirect('/admin/users')->with('status', "Der Nutzer {$user->getFullName()} wurde gelöscht");
    }

    /**
     * Renders a page to confirm the (soft-)deletion of a user.
     *
     * @param \App\User $user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function confirmDestroy(User $user) {
        $this->authorize('delete', $user);

        return view('users.confirmdelete', compact('user'));
    }

    /**
     * Handles a request to update the last_training property of multiple users.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateTraining(Request $request) {
        $date_format = config('dienstplan.date_format');
        $min_date = config('dienstplan.min_date');
        $max_date = config('dienstplan.max_date');
        $validated = $this->validate($request, [
            'users' => 'required|array|min:1',
            'users.*' => 'required|integer|exists:users,id',
            'last_training' => "required|date_format:${date_format}|after:${min_date}|before:${max_date}",
        ]);

        $users = User::find($validated['users']);
        $lastTraining = Carbon::parse($validated['last_training']);

        try {
            DB::transaction(function () use ($users, $lastTraining) {
                foreach ($users as $user) {
                    $user->last_training = $lastTraining;
                    $this->authorize('update', $user);
                    $user->save();
                }
            });
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Error updating last training', [ $users, $e ]);
            return back()->withInput($request->all())->withErrors('Unterweisung konnte nicht aktualisiert werden');
        }

        return redirect('admin/users');
    }

    /**
     * Checks whether a register_token is still valid and returns a redirect
     * with an error message otherwise.
     *
     * If it has indeed expired, the token is regenerated, stored and mailed to
     * the user again.
     *
     * @param \App\User $user
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function checkRegistration(User $user) {
        $expired_at = now()->sub(config('dienstplan.expiry.user_register_token'));

        /*
         * Here we check whether the token is too old. This assumes that a user
         * is not anymore updated other than by himself to complete the
         * registration. This is not strictly required. But for an
         * implementation where only administrator can edit other users, it
         * should be sufficiently applicable.
         */
        if ($user->updated_at < $expired_at) {
            $user->register_token = str_random(60);
            $user->save();
            $user->sendRegisterNotification($user->register_token);

            return redirect(url('/login'))->with(
                'errors',
                'Deine Anmeldung war abgelaufen, weswegen wir dir eine neue Registrierungsmail geschickt haben'
            );
        }

        return null;
    }

    /**
     * Re-authenticates the currently logged in user using <code>$password</code>.
     *
     * @param $password
     *
     * @return bool
     */
    protected function reauthenticate($password) {
        $credentials = [
            'id' => Auth::id(),
            'password' => $password,
        ];

        return Auth::guard()->validate($credentials);
    }

    /**
     * Overwrites the guard used during password reset by the
     * {@see ResetsPasswords} trait.
     *
     * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
     * @see ResetsPasswords::guard()
     */
    protected function guard() {
        return Auth::guard('web');
    }

}