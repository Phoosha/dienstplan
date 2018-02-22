<?php

namespace Tests\Feature;

use App\Notifications\ResetPassword;
use App\User;
use Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Notification;
use Tests\TestCaseWithAuth;

class AuthRoutesTest extends TestCaseWithAuth {

    use WithFaker, DatabaseTransactions;

    protected $password;
    protected $user;
    protected $other_user;

    protected function setUp() {
        parent::setUp();
        $this->password = $this->faker->password;
        $this->user = factory(User::class)->create([
            'password' => Hash::make($this->password)
        ]);
        $this->other_user = factory(User::class)->make();
    }

    protected function triggerLogin(User $user, string $password) {
        $this->get('/login');

        return $this->post('/login', [
            '_token' => csrf_token(),
            'login' => $user->login,
            'password' => $password,
            'remember' => false,
        ]);
    }

    protected function triggerPasswordResetEmail(User $user) {
        Notification::fake();

        $this->get('/password/reset');

        return $this->post('/password/email', [
            '_token' => csrf_token(),
            'email' => $user->email
        ]);
    }

    public function testIndexRedirectsToLoginAsGuest() {
        $response = $this->get('/');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function testLoginReturnsOKAsGuest() {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $this->assertGuest();
    }

    /**
     * @depends testLoginReturnsOKAsGuest
     */
    public function testLoginWithValidCredentialsRedirectsToIndexAsUserWithoutErrors() {
        $response = $this->triggerLogin($this->user, $this->password);

        $response->assertRedirect('/')->assertSessionMissing('errors');
        $this->assertAuthenticatedAs($this->user);
    }

    /**
     * @depends testLoginReturnsOKAsGuest
     */
    public function testLoginWithInvalidCredentialsRedirectsToLoginAsGuestWithErrors() {
        $response = $this->triggerLogin($this->other_user, $this->password);

        $response->assertRedirect('/login')->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function testLogoutRedirectsToIndexAsGuest() {
        $response = $this->actingAs($this->user)->get('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function testPasswordResetReturnsOKAsGuest() {
        $response = $this->get('/password/reset');

        $response->assertStatus(200);
        $this->assertGuest();
    }

    /**
     * @depends testPasswordResetReturnsOKAsGuest
     */
    public function testPasswordEmailWithValidEmailSendsEmailAndRedirectsBackAsGuestWithStatus() {
        $response = $this->triggerPasswordResetEmail($this->user);

        $response->assertRedirect('/password/reset')->assertSessionHas('status');
        Notification::assertSentTo($this->user, ResetPassword::class);
        $this->assertGuest();
    }

    /**
     * @depends testPasswordResetReturnsOKAsGuest
     */
    public function testPasswordEmailWithInvalidEmailRedirectsBackAsGuestWithErrors() {
        $response = $this->triggerPasswordResetEmail($this->other_user);

        $response->assertRedirect('/password/reset')->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function testPasswordResetWithTokenReturnsOKAsGuest() {
        $token = str_random(10);
        $response = $this->get("/password/reset/{$token}");

        $response->assertStatus(200);
        $this->assertGuest();
    }

    /**
     * @depends testPasswordResetWithTokenReturnsOKAsGuest
     * @depends testPasswordEmailWithValidEmailSendsEmailAndRedirectsBackAsGuestWithStatus
     */
    public function testActualPasswordResetWithValidCredentialsRedirectsToIndexAsUserWithStatus() {
        $this->triggerPasswordResetEmail($this->user);

        $token       = Notification::sent($this->user, ResetPassword::class)->first()->token;
        $newPassword = $this->faker->password;

        $response = $this->post("/password/reset", [
            '_token' => csrf_token(),
            'token' => $token,
            'email' => $this->user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword
        ]);

        $response->assertRedirect('/')->assertSessionHas('status');
        $this->assertAuthenticatedAs($this->user);
    }

    public function testActualPasswordResetWithInvalidTokenRedirectsBackAsGuestWithErrors() {
        $newPassword = $this->faker->password;

        $response = $this->post("/password/reset", [
            '_token' => csrf_token(),
            'token' => str_random(10),
            'email' => $this->user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword
        ]);

        $response->assertRedirect('/')->assertSessionHasErrors();
        $this->assertGuest();
    }

}
