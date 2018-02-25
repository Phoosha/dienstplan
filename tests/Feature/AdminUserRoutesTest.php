<?php

namespace Tests\Feature;

use App\Notifications\CompleteRegistration;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Notification;
use Tests\TestCaseWithAuth;

class AdminUserRoutesTest extends TestCaseWithAuth {

    use DatabaseTransactions;

    /**
     * @var User
     */
    protected $user;

    protected function setUp() {
        parent::setUp();

        $this->user = User::inRandomOrder()
            ->whereNotIn('id', $this->userIds)->first();
    }

    public function testAdminRedirectsToAdminUsers() {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/users');
    }

    /**
     * @dataProvider userProvider
     */
    public function testUsersReturnsOKOnlyIfAdmin($user) {
        $this->user->last_training = now()->startOfDay();
        $this->user->save();

        $response = $this->actingByString($user)->get('/admin/users');

        if (! $this->adminOnlyAssertions($response))
            $response->assertStatus(200);
    }

    /**
     * @dataProvider userProvider
     */
    public function testUsersCreateReturnsOKOnlyIfAdmin($user) {
        $response = $this->actingByString($user)->get('/admin/users/create');

        if (! $this->adminOnlyAssertions($response))
            $response->assertStatus(200);
    }

    /**
     * @dataProvider userProvider
     */
    public function testsPostUserRedirectsToUsersCreateWithoutErrorsWithStorePerformedAndEmailSentOnlyIfAdmin($asUser) {
        Notification::fake();

        // setup the user to store
        $user = $this->user->attributesToArray();
        $user['last_training'] = 'nie';
        $this->user->delete();
        $this->user->forceDelete();

        $response = $this->actingByString($asUser)->post('/admin/users', $user);

        if ($this->adminOnlyAssertions($response))
            return;

        // mimic the transformations made during post for the database lookup
        $user['last_training'] = null;
        $user['created_at'] = $user['updated_at'] = now();
        unset($user['id'], $user['register_token']);

        $response->assertRedirect('/admin/users/create')->assertSessionMissing('errors');
        $this->assertDatabaseHas('users', $user);
        Notification::assertSentTo(
            User::where('login', $user['login'])->first(),
            CompleteRegistration::class
        );
    }

    /**
     * @dataProvider userProvider
     */
    public function testPutUsersTrainingRedirectsToUsersWithoutErrorsWithUpdatePerformedOnlyIfAdmin($asUser) {
        $users = User::inRandomOrder()->take(5)->pluck('id')->all();
        $last_training = now()->format(config('dienstplan.date_format'));

        $response = $this->actingByString($asUser)
            ->put('/admin/users/training', compact('users', 'last_training'));

        if ($this->adminOnlyAssertions($response))
            return;

        var_dump(User::whereIn('id', $users)->get()->map->attributesToArray());

        $users = Collection::make($users)->map(function ($id) use ($last_training) {
            return [
                'id' => $id,
                'last_training' =>
                    Carbon::createFromFormat(config('dienstplan.date_format'), $last_training)
                        ->startOfDay(),
            ];
        });

        $response->assertRedirect('/admin/users')->assertSessionMissing('errors');
        $users->each(function ($user) {
            $this->assertDatabaseHas('users', $user);
        });
    }

    /**
     * @dataProvider userProvider
     */
    public function testUsersDeleteReturnsOKOnlyIfAdmin($asUser) {
        $response = $this->actingByString($asUser)->get("/admin/users/{$this->user->id}/delete");

        if (! $this->adminOnlyAssertions($response))
            $response->assertStatus(200);
    }

    /**
     * @dataProvider userProvider
     */
    public function testDeleteUserRedirectsToUsersWithoutErrorsWithSoftDeletePerformedOnlyIfAdmin($asUser) {
        $response = $this->actingByString($asUser)->delete("/admin/users/{$this->user->id}");

        if ($this->adminOnlyAssertions($response))
            return;

        $this->user->deleted_at = $this->user->updated_at = now();

        $response->assertRedirect('/admin/users')->assertSessionMissing('errors');
        $this->assertDatabaseHas('users', $this->user->attributesToArray());
    }

    /**
     * @dataProvider userProvider
     */
    public function testUsersEditReturnsOKOnlyIfAdmin($asUser) {
        $response = $this->actingByString($asUser)->get("/admin/users/{$this->user->id}");

        if (! $this->adminOnlyAssertions($response))
            $response->assertStatus(200);
    }

    /**
     * @dataProvider userProvider
     */
    public function testPutUsersTrashedRestoreRedirectsToUsersWithoutErrorsWithUndeletePerformedOnlyIfAdmin($asUser) {
        $this->user->delete();

        $response = $this->actingByString($asUser)
            ->put("/admin/users/trashed/{$this->user->id}/restore");

        if ($this->adminOnlyAssertions($response))
            return;

        $this->user->deleted_at = null;
        $this->user->updated_at = now();

        $response->assertRedirect("/admin/users/{$this->user->id}")
            ->assertSessionMissing('errors');
        $this->assertDatabaseHas('users', $this->user->attributesToArray());
    }

    /**
     * @dataProvider userProvider
     */
    public function testUsersTrashedDeleteReturnsOKOnlyIfAdmin($asUser) {
        $this->user->delete();

        $response = $this->actingByString($asUser)
            ->get("/admin/users/trashed/{$this->user->id}/delete");

        if (! $this->adminOnlyAssertions($response))
            $response->assertStatus(200);
    }

    /**
     * @dataProvider userProvider
     */
    public function testDeleteTrashedUserRedirectsToUsersWithoutErrorsWithDestroyPerformedOnlyIfAdmin($asUser) {
        $this->user->delete();

        $response = $this->actingByString($asUser)
            ->delete("/admin/users/trashed/{$this->user->id}");

        if ($this->adminOnlyAssertions($response))
            return;

        $response->assertRedirect('/admin/users#trash')->assertSessionMissing('errors');
        $this->assertDatabaseMissing('users', [ 'id' => $this->user->id ]);
    }

}
