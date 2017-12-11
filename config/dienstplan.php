<?php

use Carbon\Carbon;
use Carbon\CarbonInterval;

return [

    /*
    |--------------------------------------------------------------------------
    | Shift Start Times
    |--------------------------------------------------------------------------
    |
    | This array of times in the format 'HH[:MM[:SS]]' represents the start
    | times of the shifts. Shifts divide an interval of 24h starting from the
    | start time of the first shift. Every shift starts at the given time and
    | ends before before the start time of the next shift. Feel free to set as
    | many shifts starting any time you want.
    | Just make sure that they are in increasing order!
    |
     */
    'shifts' => [
        '6',
        '18',
    ],

    /*
    |--------------------------------------------------------------------------
    | Shift Past Threshold
    |--------------------------------------------------------------------------
    |
    | This threshold determines which shifts to consider as past. All shifts of
    | a day are past if the last shift of that day ends before this threshold
    | time.
    |
    */
    'past_threshold' => Carbon::parse('10 hours ago'),

    'dropdown_time_steps' => CarbonInterval::minutes(30),
    'dropdown_time_format' => 'H:i',

];
