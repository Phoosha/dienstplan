<?php

namespace Tests\Feature;

use App\User;
use Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCaseWithAuth;

class UserRoutesTest extends TestCaseWithAuth {

    use WithFaker, DatabaseTransactions;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var User
     */
    protected $user;

    protected function setUp() {
        parent::setUp();
        $this->password = $this->faker->password;
        $this->user = factory(User::class)->create([
            'password' => Hash::make($this->password)
        ]);
    }

    /**
     * @dataProvider userProvider
     */
    public function testUserReturnsOKUnlessGuest($user) {
        $response = $this->actingByString($user)->get('/user');

        if (! $this->guestAssertions($response))
            $response->assertStatus(200);
    }

    /**
     * @dataProvider userProvider
     */
    public function testRegisterWithValidTokenReturnsOK($user) {
        $this->user->register_token = str_random(10);
        $this->user->save();

        $response = $this->actingByString($user)
            ->get("/register?register_token={$this->user->register_token}");

        $response->assertStatus(200);
    }

    /**
     * @dataProvider userProvider
     */
    public function testRegisterWithInvalidTokenRedirectsToLogin($user) {
        $token = str_random(10);

        $response = $this->actingByString($user)
            ->get("/register?register_token={$token}");

        $response->assertRedirect('/login');
    }

    /**
     * @depends testUserReturnsOKUnlessGuest
     */
    public function testPutUserAsUserRedirectsBackWithoutErrorsWithUpdatePerformed() {
        $this->user->refresh();
        $updates = [
            'login' => 'aaffe',
            'first_name' => 'Alice',
            'last_name' => 'Affenzahn',
            // vvv cannot be left out because storage and request representation differ
            'last_training' => $this->user->last_training
                ->format(config('dienstplan.date_format')),
        ];

        $this->actingAs($this->user)->get('/user');
        $response = $this->actingAs($this->user)
            ->put("/users/{$this->user->id}",
                array_merge($this->user->attributesToArray(), $updates)
            );

        $updates['last_training'] = $this->user->last_training;
        $this->user->refresh();

        $response->assertRedirect('/user')->assertSessionMissing('errors');
        self::assertEquals(
            array_intersect($this->user->attributesToArray(), $updates),
            $updates
        );
    }

    public function testPutUserPasswordAsUserRedirectsBackWithoutErrorsWithPasswordReset() {
        $newPassword     = $this->faker->password;
        $oldPasswordHash = $this->user->password;

        $this->actingAs($this->user)->get('/user');
        $response = $this->actingAs($this->user)->put(
            "/users/{$this->user->id}/password", [
                'password' => $this->password,
                'new-password' => $newPassword,
                'new-password_confirmation' => $newPassword,
            ]);

        $this->user->refresh();

        $response->assertRedirect('/user')->assertSessionMissing('errors');
        self::assertNotEquals($oldPasswordHash, $this->user->password);
    }

    public function testDeleteUserTokenAsUserRedirectsBackWithoutErrorsWithTokenCycled() {
        // only first access initializes the api_token
        $this->actingAs($this->user)->get('/user');
        $this->user->refresh();
        $oldToken = $this->user->api_token;

        $response = $this->actingAs($this->user)
            ->delete("/users/{$this->user->id}/api_token");

        $this->user->refresh();

        $response->assertRedirect('/user');
        self::assertNotEquals($oldToken, $this->user->api_token);
    }

}
