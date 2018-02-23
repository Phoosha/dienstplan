<?php

namespace Tests\Feature;

use App\Phone;
use Auth;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCaseWithAuth;

class PhoneRoutesTest extends TestCaseWithAuth {

    use DatabaseTransactions;

    /**
     * @var Phone
     */
    protected $phone;

    protected function setUp() {
        parent::setUp();
        $this->phone = factory(Phone::class)->make();
    }

    /**
     * @dataProvider userProvider
     */
    public function testPhonesReturnsOKUnlessGuest($user) {
        $response = $this->actingByString($user)->get('/phones');

        if (! $this->guestAssertions($response))
            $response->assertStatus(200);
    }

    /**
     * @dataProvider userProvider
     */
    public function testPostPhoneRedirectsToPhonesWithoutErrorsWithStorePerformedOnlyIfAdmin($user) {
        $response = $this->actingByString($user)
            ->post('/phones', $this->phone->attributesToArray());

        if ($this->guestAssertions($response))
            return;
        if (! Auth::user()->is_admin) {
            $response->assertStatus(403);

            return;
        }

        $response->assertRedirect('/phones')->assertSessionMissing('errors');
        $this->assertDatabaseHas('phones', $this->phone->refresh()->attributesToArray());
    }

    /**
     * @dataProvider userProvider
     */
    public function testPhonesEditReturnsOKOnlyIfAdmin($user) {
        $response = $this->actingByString($user)->get('/phones/edit');

        if ($this->guestAssertions($response))
            return;
        if (! Auth::user()->is_admin) {
            $response->assertStatus(403);

            return;
        }

        $response->assertStatus(200);
    }

    /**
     * @dataProvider userProvider
     */
    public function testDeletePhoneRedirectsToPhonesEditWithoutErrorsWithDestroyPerformedOnlyIfAdmin($user) {
        $this->phone->save();

        $response = $this->actingByString($user)->delete("/phones/{$this->phone->id}");

        if ($this->guestAssertions($response))
            return;
        if (! Auth::user()->is_admin) {
            $response->assertStatus(403);

            return;
        }

        $response->assertRedirect('/phones/edit')->assertSessionMissing('errors');
        $this->assertDatabaseMissing('phones', $this->phone->attributesToArray());
    }

}
