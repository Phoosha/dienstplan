<?php

namespace App\Http\Controllers;

use App\User;

class TrashedUserController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Renders a confirmation page before destroying a trashed
     * <code>User</code>.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function confirmDestroy(int $id) {
        $user = $this->findTrashedUser($id);

        $this->authorize('forceDelete', $user);

        return view('admin.users.confirmdelete', compact('user'));
    }

    /**
     * Force deletes a trashed (i.e. soft-deleted) <code>User</code>.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(int $id) {
        $user = $this->findTrashedUser($id);

        $this->authorize('forceDelete', $user);
        $user->forceDelete();

        return redirect(url('admin/users#trash'))
            ->with('trash-status', "Nutzer {$user->getFullName()} wurde unwiederbringlich gelöscht");
    }

    /**
     * Restores (i.e. un-deletes) a trashed <code>User</code>.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function restore(int $id) {
        $user = $this->findTrashedUser($id);

        $this->authorize('restore', $user);
        $user->restore();

        return redirect(url('admin/users', $id))
            ->with('status', 'Nutzer wurde wiederhergestellt');
    }

    /**
     * Finds a trashed user by <code>$id</code>.
     *
     * @param $id
     *
     * @return User
     */
    protected function findTrashedUser($id) {
        return User::onlyTrashed()->where('id', $id)->first();
    }

}
