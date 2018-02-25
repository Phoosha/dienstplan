<?php

namespace Tests;

use App\User;
use Auth;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Collection;

abstract class TestCaseWithAuth extends TestCase {

    use CreatesApplication;

    /**
     * @var User[]
     */
    protected $users;

    /**
     * @var int[]
     */
    protected $userIds;

    protected function setUp() {
        parent::setUp();
        $this->users = [
            'normal' => factory(User::class)->create(),
            'admin' => factory(User::class)->create([ 'is_admin' => true ]),
        ];
        $this->userIds = Collection::make($this->users)->pluck('id');
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

    protected function adminOnlyAssertions(TestResponse $response) {
        if ($this->guestAssertions($response))
            return true;

        if (! Auth::user()->is_admin) {
            $response->assertStatus(403);

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
