<?php

namespace App\Http\Controllers;

use App\Duty;
use App\Http\Requests\CreateDuty;
use App\Http\Requests\StoreDuty;
use App\Shift;
use App\Slot;
use Throwable;

class DutyController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public function index($year = null, $month = null) {
        // first: determine the month to render
        $month_start = firstOfMonth($year, $month);

        /*
         * -- Generate Calendar --
         */
        $weeks = calendar($month_start);

        /*
         * -- Generate Shift Table
         */
        $first_shift = Shift::firstOfDay($month_start);

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
        if (Slot::active($prev_month)->isNotEmpty() && hasValidYear($prev_month))
            $prev = [ $prev_month->year, $prev_month->month ];
        if (Slot::active($next_month)->isNotEmpty() && hasValidYear($next_month))
            $next = [ $next_month->year, $next_month->month ];

        /*
         * -- Slot Config --
         */
        $slots = Slot::active($month_start);
        if ($slots->isEmpty())
            abort(404);

        return view('duties.index', compact(
            'weeks', 'month_start', 'prev_month', 'prev', 'next_month', 'next', 'days', 'slots'
        ));
    }

    public function create(CreateDuty $request) {
        // Check if we want to create duties from a selection of shifts
        if (isset($request['year'])) {
            return $this->createFromShifts($request);
        }

        // Otherwise present the current shift
        $duty = Shift::create()->toDuty();
        $duty->slot()->associate(
            $duty->availableSlots()->first()
        );
        $from_scratch = true;

        return view('duties.create', compact('duty', 'from_scratch'));
    }

    public function createFromShifts(CreateDuty $request) {
        $duties       = $request->getDuties();
        $from_scratch = false;

        return view('duties.create', compact('duties', 'from_scratch'));
    }

    public function edit($id) {
        $duty = Duty::find($id);

        return view('duties.edit', compact('duty'));
    }

    public function store(StoreDuty $request) {
        try {
            $duties = $request->persist();

            return redirect(self::planWithDuty($duties->first()));
        } catch (Throwable $e) {
            return back()->withInput($request->all())->withErrors('Dienst konnte nicht gespeichert werden.');
        }
    }

    public function update() {
        // FIXME
        dd('update');
    }

    public function destroy($id) {
        $duty = Duty::find($id);
        $duty->delete();

        return redirect(self::planWithDuty($duty));
    }

    /**
     * Return a URI that show the plan with <code>$duty</code>.
     *
     * @param Duty $duty
     * @return string
     */
    private static function planWithDuty(Duty $duty) {
        $start  = $duty->start;

        return "plan/{$start->year}/{$start->month}#day-{$start->day}";
    }

}
