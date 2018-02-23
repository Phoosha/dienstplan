<?php

namespace App\Http\Controllers;

use App\Phone;
use App\User;
use Illuminate\Http\Request;

class PhoneController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public function index(bool $edit = false) {
        $users = User::ordering()->get();
        $phones = Phone::ordering()->get();

        return view('phones.index', compact('users', 'phones', 'edit'));
    }

    public function edit() {
        $this->authorize('edit', Phone::class);

        return $this->index(true);
    }

    public function store(Request $request) {
        $this->authorize('store', Phone::class);

        $this->validate($request, [
            'name' => 'required|string|max:70',
            'phone' => [ 'present', 'max:35', 'regex:' . config('dienstplan.phone_regex') ],
        ]);

        Phone::create($request->all());

        return redirect('phones')->with('status', 'Neuer Telefoneintrag erfolgreich angelegt');
    }

    public function destroy(Phone $phone) {
        $this->authorize('delete', $phone);
        $phone->delete();

        return redirect('phones/edit')->with('status', 'Telefoneintrag erfolgreich gelöscht');
    }

}
