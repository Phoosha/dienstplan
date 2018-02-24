<?php

namespace App\Http\Controllers;

class AdminController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public function reports() {
        return "FIXME";
    }

    public function slots() {
        return "FIXME";
    }

}
