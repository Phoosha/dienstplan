<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class DutyController extends Controller
{
    public function index($year = null, $month = null) {
        $today = Carbon::now();

        $month_start = Carbon::createFromDate($year, $month, 1)->firstOfMonth();
        $numWeeks    = $month_start->copy()->lastOfMonth()->weekOfMonth;
        $cal_start   = $month_start->copy()->startOfWeek();

        $weeks = [];
        $day = $cal_start->copy();
        for ($week = 0; $week < $numWeeks; $week++) {
            $weeks[$week] = [];
            for ($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++) {
                $weeks[$week][] = $day;
                $day = $day->copy()->addDay();
            }
        }

        $prev_month = $month_start->copy()->subMonth();
        $next_month = $month_start->copy()->addMonth();

        $prev = [ $prev_month->year, $prev_month->month ];
        $next = [ $next_month->year, $next_month->month ];

        return view('duties.index', compact('weeks', 'month_start', 'prev', 'next'));
    }
}
