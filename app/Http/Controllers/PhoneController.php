<?php

namespace App\Http\Controllers;

use App\Phone;
use App\User;
use Illuminate\Http\Request;

class PhoneController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $users = User::orderBy('last_name')->get();
        $phones = Phone::orderBy('name')->get();

        return view('phones.index', compact('users', 'phones'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Phone $phone
     * @return \Illuminate\Http\Response
     */
    public function show(Phone $phone) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Phone $phone
     * @return \Illuminate\Http\Response
     */
    public function edit(Phone $phone) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Phone $phone
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Phone $phone) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Phone $phone
     * @return \Illuminate\Http\Response
     */
    public function destroy(Phone $phone) {
        //
    }

}
