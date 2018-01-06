<?php

namespace App\Http\Controllers;

use App\Duty;
use App\Http\Requests\CreateDutyFromShifts;
use App\Shift;
use App\SlotConfig;
use Carbon\Carbon;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DutyController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Safely get a <code>Carbon</code> instance for the first day of the
     * month from <code>$year</code> and <code>$month</code> as integers.
     *
     * Throws a <code>HttpException</code> if <code>$year</code> or
     * <code>$month</code> are invalid.
     *
     * @param $year
     * @param $month
     * @return \Carbon\Carbon
     * @throws HttpException,NotFoundHttpException
     */
    private static function firstOfMonth($year, $month) {
        $year  = isset($year)  ? (int) $year  : $year;
        $month = isset($month) ? (int) $month : $month;

        try {
            $month_start = Carbon::createSafe($year, $month, 1, 0)->firstOfMonth();
            if (! hasValidYear($month_start))
                abort(404);
            return $month_start;
        } catch (InvalidArgumentException $e) {
            abort(400, $e->getMessage());
        }
    }

    public function index($year = null, $month = null) {
        // first: determine the month to render
        $month_start = self::firstOfMonth($year, $month);

        /*
         * -- Generate Calendar --
         */
        // the calendars displays whole weeks only
        $cal_start   = $month_start->copy()->startOfWeek();
        $numWeeks    = $month_start->copy()->lastOfMonth()->weekOfMonth;

        $weeks = [];
        $day = $cal_start->copy();
        for ($week = 0; $week < $numWeeks; $week++) {
            $weeks[$week] = [];
            for ($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++) {
                $weeks[$week][] = $day;
                $day = $day->copy()->addDay();
            }
        }

        /*
         * -- Generate Shift Table
         */
        $first_shift  = Shift::firstOfDay($month_start);

        $days = [];
        $shift = $first_shift->copy();
        for ($day = 0; $day < $month_start->daysInMonth; $day++) {
            $days[$day] = [];
            do {
                $days[$day][] = $shift;
                $shift = $shift->copy()->next();
            } while (! $shift->isFirstShift());
        }

        /*
         * -- Navigation --
         */
        $prev_month = $month_start->copy()->subMonth();
        $next_month = $month_start->copy()->addMonth();
        $prev = $next = null;
        if (SlotConfig::active($prev_month) !== null && hasValidYear($prev_month))
            $prev = [ $prev_month->year, $prev_month->month ];
        if (SlotConfig::active($next_month) !== null && hasValidYear($next_month))
            $next = [ $next_month->year, $next_month->month ];

        /*
         * -- Slot Config --
         */
        $slots = SlotConfig::activeOrFail($month_start)->slots;
        if (empty($slots))
            return abort(404);

        return view('duties.index', compact(
            'weeks', 'month_start', 'prev_month', 'prev', 'next_month', 'next', 'days', 'slots'
        ));
    }

    public function create(CreateDutyFromShifts $request) {
        // Check if we want to create duties from a selection of shifts
        if (isset($request['year'])) {
            return $this->createFromShifts($request);
        }

        // Otherwise present the current shift
        $duty  = Shift::create()->toDuty();
        $duty->slot()->associate($duty->availableSlots()->first());
        $duties = [ $duty ];
        $from_scratch = true;

        return view('duties.create', compact('duties', 'from_scratch'));
    }

    public function createFromShifts(CreateDutyFromShifts $request) {
        $month_start = self::firstOfMonth($request['year'], $request['month']);

        $duties = [];
        foreach ($request['shifts'] as $day => $dayOfShifts) {
            if ($day < 1 || $day >= $month_start->daysInMonth)
                return back();
            foreach ($dayOfShifts as $shift => $slot_id) {
                if ($shift < 0 || $shift >= Shift::shiftsPerDay())
                    return back();

                $duty = Shift::create($month_start->year, $month_start->month, $day, $shift)->toDuty();
                $duty->slot_id = (int) $slot_id;
                $duties[] = $duty;
            }
        }

        $duties       = Duty::mergeAll($duties);
        $from_scratch = false;

        return view('duties.create', compact('duties', 'from_scratch'));
    }

    public function edit($id) {
        // FIXME
        $duty = Duty::createFromShift(Shift::create());
        $duty->id = 10;
        return view('duties.edit', compact('duty'));
    }

    public function store(Request $request) {
        dd(request()->all());
    }

    public function update($id) {
        // FIXME
        dd('update');
    }

    public function destroy($id) {
        // FIXME
        dd('destroy');
    }

}
