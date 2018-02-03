<?php

namespace App\Auth;

use Illuminate\Auth\TokenGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

/**
 * Authentication guard using the <code>register_token</code> for authentication.
 *
 * @package App\Auth
 */
class RegisterTokenGuard extends TokenGuard {

    public function __construct(UserProvider $provider, Request $request) {
        parent::__construct($provider, $request);
        $this->inputKey = 'register_token';
        $this->storageKey = 'register_token';
    }

}