<?php

namespace App\Http\Controllers;

use App\Duty;
use App\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DutyController extends Controller
{
    public function index($year = null, $month = null) {
        // first: determine the month to render
        $month_start = Carbon::createFromDate($year, $month, 1)->firstOfMonth();

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

    public function verify() {
        $year = request('year');
        $month = request('month');

        $shifts = [];
        foreach (request()->all() as $key => $val) {
            $ids = explode('-', $key);
            if ($ids[0] !== 'shift') continue;

            $day   = (int) $ids[1];
            $shift = (int) $ids[2];
            $slot  = (int) $val;

            $shifts[] = Shift::create($year, $month, $day, $shift)->setSlot($slot);
        }

        $duties = Duty::createFromShifts($shifts);

        return view('duties.create', compact('duties'));
    }

    public function create() {

    }

    public function store(Request $request) {

    }
}
