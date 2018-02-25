<?php

namespace Tests\Feature;

use App\Duty;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiRoutesTest extends TestCase {

    use WithFaker, DatabaseTransactions;

    const NUM_DUTIES = 20;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $duties;

    protected function setUp() {
        parent::setUp();

        $this->user = User::inRandomOrder()->first();
        $this->user->cycleApiToken();

        Duty::destroy(Duty::all('id')->pluck('id')->all());

        $this->duties = factory(Duty::class, self::NUM_DUTIES)->make()
            ->each(function (Duty $duty) {
                $duty->user()->associate($this->user);
            });

        $start    = Carbon::now()->sub(config('dienstplan.duty.view_past'));
        $end      = Carbon::now()->add(config('dienstplan.duty.store_future'));
        $distance = (int) ( $end->diffInSeconds($start) / self::NUM_DUTIES );
        /** @var Duty $duty */
        foreach ($this->duties as $duty) {
            $duty->start = $start;
            $duty->end   = $start->copy()->addSeconds(
                $this->faker->numberBetween(60*60, $distance)
            );

            $duty->save();

            $start->addSeconds($distance);
        }
    }

    public function testIcsDutiesWithValidTokenReturnsOKWith20VEVENTs() {
        $response = $this->get("/api/ics/duties?api_token={$this->user->api_token}");

        $response->assertStatus(200);
        self::assertEquals(20, substr_count($response->content(), 'BEGIN:VEVENT'));
    }

    public function testIcsDutiesWithInvalidTokenRedirectsToLogin() {
        $token = str_random(10);
        $response = $this->get("/api/ics/duties?api_token={$token}");

        $response->assertRedirect('/login');
    }

}
