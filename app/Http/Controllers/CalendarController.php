<?php

namespace App\Http\Controllers;

use App\Duty;
use Auth;
use Carbon\Carbon;

class CalendarController extends Controller {

    public function view() {
        $duties = Duty::takenBy(Auth::user())
            ->between(
                Carbon::now()->sub(config('dienstplan.duty.view_past')),
                Carbon::now()->add(config('dienstplan.duty.store_future'))
            )->get();
        $cal_name  = config('app.name') . ' ' . Auth::user()->getFullName();
        $file_name = str_replace(' ', '-', $cal_name) . '.ics';

        return response(iCalendar($duties, "PUBLISH", null, Auth::user()->getCalendarURL()))
            ->header('Content-Type', 'text/calendar; charset=utf-8; method="PUBLISH"')
            ->header('Content-Disposition', "attachment; filename={$file_name}");
    }

}
