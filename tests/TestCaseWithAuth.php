<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestResponse;

abstract class TestCaseWithAuth extends TestCase {

    use CreatesApplication;

    /**
     * @var User[]
     */
    protected $users;

    protected function setUp() {
        parent::setUp();
        $this->users = [
            'normal' => factory(User::class)->create(),
            'admin' => factory(User::class)->create([ 'is_admin' => true ]),
        ];
    }

    protected function actingByString($user, $driver = null) {
        if (isset($user)) {
            return parent::actingAs($this->users[$user], $driver);
        }

        return $this;
    }

    protected function guestAssertions(TestResponse $response) {
        if (! $this->isAuthenticated()) {
            $response->assertRedirect('/login');

            return true;
        }

        return false;
    }

    public function userProvider() {
        return [
            'guest' => [ null ],
            'normal' => [ 'normal' ],
            'admin' => [ 'admin' ],
        ];
    }

}
