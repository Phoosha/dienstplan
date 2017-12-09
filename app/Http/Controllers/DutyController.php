<?php

namespace App\Http\Controllers;

use App\Shift;
use Carbon\Carbon;

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
        $first_shift  = Shift::firstOfDay($cal_start);
        $shiftsPerDay = Shift::shiftsPerDay();

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

        /*
         * -- Misc --
         */
        $past_threshold = Carbon::parse(config('dienstplan.past_threshold'));

        return view('duties.index', compact(
            'weeks', 'month_start', 'prev', 'next', 'days', 'shiftsPerDay', 'past_threshold'
        ));
    }
}
