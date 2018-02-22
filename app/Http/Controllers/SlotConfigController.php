<?php

namespace App\Http\Controllers;

class SlotConfigController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

}
