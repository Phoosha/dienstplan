<?php

namespace Tests\Feature;

use App\Duty;
use App\Slot;
use App\SlotConfig;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCaseWithAuth;

class DutyRoutesTest extends TestCaseWithAuth {

    use WithFaker, DatabaseTransactions;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var array
     */
    protected $dutyAsRequest;

    protected function setUp() {
        parent::setUp();

        $this->user = factory(User::class)->create();

        $start = now()->addDay();
        $this->dutyAsRequest = [
            'user_id' => $this->user->id,
            'slot_id' => Slot::allActive($start)->first()->id,
            'start-date' => $start->format(config('dienstplan.date_format')),
            'end-date' => $start->copy()->addDay()->format(config('dienstplan.date_format')),
            'start-time' => '08:30',
            'end-time' => '10:00',
            'type' => Duty::NORMAL,
        ];
    }

    protected function dutyFromRequest($dutyAsRequest) {
        Duty::unguard();
        $duty = Duty::make([
            'user_id' => $dutyAsRequest['user_id'],
            'slot_id' => $dutyAsRequest['slot_id'],
            'start' => Carbon::createFromFormat(
                config('dienstplan.date_format'),
                $dutyAsRequest['start-date']
            )->setTimeFromTimeString($dutyAsRequest['start-time']),
            'end' => Carbon::createFromFormat(
                config('dienstplan.date_format'),
                $dutyAsRequest['end-date']
            )->setTimeFromTimeString($dutyAsRequest['end-time']),
            'type' => $dutyAsRequest['type']
        ]);
        Duty::reguard();

        return $duty;
    }

    protected function setUpSlotConfigWithMinDate() {
        SlotConfig::all()->each->delete();
        $config = SlotConfig::createWithSlots(
            Carbon::instance(config('dienstplan.min_date')),
            [ '79/1', '10/1' ]
        );
        $config->save();
    }

    public function monthAndYearProvider() {
        return [
            'min date'  => [ 1970, 1,  200 ],
            'max date'  => [ 2037, 12, 200 ],
            'January'   => [ 2017, 1,  200 ],
            'February'  => [ 2017, 2,  200 ],
            'March'     => [ 2017, 3,  200 ],
            'April'     => [ 2017, 4,  200 ],
            'May'       => [ 2017, 5,  200 ],
            'June'      => [ 2017, 6,  200 ],
            'July'      => [ 2017, 7,  200 ],
            'August'    => [ 2017, 8,  200 ],
            'September' => [ 2017, 9,  200 ],
            'October'   => [ 2017, 10, 200 ],
            'November'  => [ 2017, 11, 200 ],
            'December'  => [ 2017, 12, 200 ],
            'below min' => [ 1969, 12, 404 ],
            'above max' => [ 2038, 1,  404 ],
            'no year'   => [ 'xy', 1,  400 ],
            'no month'  => [ 2017, 'x', 400 ],
        ];
    }

    /**
     * @dataProvider monthAndYearProvider
     */
    public function testPlanWithYearAndMonthReturnsCorrectResponse($year, $month, $expected) {
        $this->setUpSlotConfigWithMinDate();
        $this->user->setAttribute('is_admin', true)->save();

        $response = $this->actingAs($this->user)->get("/plan/{$year}/{$month}");

        $response->assertStatus($expected);

        if ($response->isSuccessful())
            $response->assertSeeText((string) $year)
                ->assertSeeText(
                    monthname(Carbon::createFromDate(null, $month))
                );
    }

    /**
     * @dataProvider userProvider
     */
    public function testPlanWithoutYearOrMonthReturnsOKShowingCurrentYearAndMonthUnlessGuest($user) {
        $response = $this->actingByString($user)->get('/plan');

        if (! $this->guestAssertions($response))
            $response->assertStatus(200)
                ->assertSeeText((string) now()->year)
                ->assertSeeText(monthname(now()));
    }

    /**
     * @dataProvider userProvider
     */
    public function testDutiesCreateReturnsOKShowingCurrentDateUnlessGuest($user) {
        $response = $this->actingByString($user)->get('/duties/create');

        if (! $this->guestAssertions($response))
            $response->assertStatus(200)
                ->assertSee(
                    now()->format(config('dienstplan.date_format'))
                );
    }

    public function testStoreDutyAsUserRedirectsToPlanWithoutErrorsWithStorePerformed() {
        Duty::destroy(Duty::all('id')->pluck('id')->all());

        $response = $this->actingAs($this->user)
            ->post('/duties', [
                '_token' => csrf_token(),
                'duties' => [ $this->dutyAsRequest ],
            ]);

        $duty = $this->dutyFromRequest($this->dutyAsRequest);
        $start = $duty->start;

        if (! $this->guestAssertions($response)) {
            $response->assertRedirect("/plan/{$start->year}/{$start->month}#day-{$start->day}")
                ->assertSessionMissing('errors');
            $this->assertDatabaseHas('duties', $duty->attributesToArray());
        }
    }

    /**
     * @dataProvider userProvider
     */
    public function testDutiesEditReturnsOKUnlessGuest($user) {
        $duty = $this->dutyFromRequest($this->dutyAsRequest);
        $duty->save();

        $response = $this->actingByString($user)->get("/duties/{$duty->id}");

        if (! $this->guestAssertions($response))
            $response->assertStatus(200);
    }

    public function testPutDutyAsAdminRedirectsToPlanWithoutErrorsWithUpdatePerformed() {
        $this->user->setAttribute('is_admin', true)->save();

        Duty::destroy(Duty::all('id')->pluck('id')->all());
        $duty = $this->dutyFromRequest($this->dutyAsRequest);
        $duty->save();

        $updates = [
            'slot_id' => Slot::allActive($duty->start)
                ->whereNotIn('id', $duty->slot_id)
                ->first()->id,
            'start-date' => now()->addDays(5)->format(config('dienstplan.date_format')),
            'end-date' => now()->addDays(5)->format(config('dienstplan.date_format')),
        ];
        $updatedDutyAsRequest = array_merge($this->dutyAsRequest, $updates);

        $response = $this->actingAs($this->user)
            ->put("/duties/{$duty->id}", [
                '_token' => csrf_token(),
                'duties' => [ $updatedDutyAsRequest ],
            ]);

        $duty = $this->dutyFromRequest($updatedDutyAsRequest);

        $response->assertRedirect(planWithDuty($duty))->assertSessionMissing('errors');
        $this->assertDatabaseHas('duties', $duty->attributesToArray());
    }

    /**
     * @dataProvider userProvider
     */
    public function testDeleteDutyRedirectsToPlanWithoutErrorsWithDestroyPerformedOnlyIfAdmin($user) {
        $duty = $this->dutyFromRequest($this->dutyAsRequest);
        $duty->save();
        $duty->refresh();

        $response = $this->actingByString($user)->delete("/duties/{$duty->id}");

        if ($this->guestAssertions($response))
            return;
        if (! Auth::user()->is_admin) {
            $response->assertStatus(403);

            return;
        }

        $response->assertRedirect(planWithDuty($duty))->assertSessionMissing('errors');
        $this->assertDatabaseMissing('duties', $duty->attributesToArray());
    }

}
