<?php

namespace App\Http\Controllers;

use App\Duty;
use App\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use InvalidArgumentException;

class DutyController extends Controller {

    private static function firstOfMonth($year, $month) {
        $year  = isset($year)  ? (int) $year  : $year;
        $month = isset($month) ? (int) $month : $month;

        try {
            return Carbon::createSafe($year, $month, 1, 0)->firstOfMonth();
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
        $prev = [ $prev_month->year, $prev_month->month ];
        $next = [ $next_month->year, $next_month->month ];

        return view('duties.index', compact(
            'weeks', 'month_start', 'prev', 'next', 'days'
        ));
    }

    public function create() {
        $request = $this->validate(request(), [
            'year' => 'integer|min:0|max:9999',
            'month' => 'required_with:year|integer|min:1|max:12',
            'shifts' => 'required_with:year|array|max:7|integer_keys',
            'shifts.*' => 'required_with:year|array|max:9|integer_keys',
            'shifts.*.*' => "required_with:year|integer|min:0|max:1"
        ]);

        if (isset($request['year'])) {
            $month_start = self::firstOfMonth($request['year'], $request['month']);

            $shifts = [];
            foreach ($request['shifts'] as $day => $dayOfShifts) {
                if ($day < 1 || $day >= $month_start->daysInMonth)
                    return back();
                foreach ($dayOfShifts as $shift => $slot) {
                    if ($shift < 0 || $shift >= Shift::shiftsPerDay())
                        return back();
                    $shifts[] = Shift::create(
                        $month_start->year,
                        $month_start->month,
                        $day,
                        $shift
                    )->setSlot($slot);
                }
            }
        }

        // If we have no shift, fallback to the current shift right now
        $from_scratch = false;
        if (empty($shifts)) {
            $shifts = [Shift::create()->setSlot(0)];
            $from_scratch = true;
        }

        // Convert shifts into duties, i.e. merge adjacent ones
        $duties = Duty::createFromShifts($shifts);

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
