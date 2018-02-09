<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ForgotPasswordController extends Controller {

    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest');
        $this->middleware('throttle:5,10')->only('sendResetLinkEmail');
    }

    /**
     * validate the email for the given request.
     *
     * @param  \illuminate\http\request  $request
     * @return void
     */
    protected function validateEmail(Request $request) {
        // ignore users who are undergoing registration
        $unresetable = User::whereNotNull('register_token')->get()->pluck('email');

        $this->validate($request, [
           'email' => [
               'required', 'email', Rule::notIn($unresetable->all()),
           ]
        ]);
    }
}
