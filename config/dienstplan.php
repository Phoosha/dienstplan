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

    /*
    |--------------------------------------------------------------------------
    | Time Steps for Dropdown Selection
    |--------------------------------------------------------------------------
    |
    | In dropdown menus choices for the selection of the time of day are spaced
    | by this interval.
    |
    */
    'dropdown_time_steps' => CarbonInterval::minutes(30),


    /*
    |--------------------------------------------------------------------------
    | Date and Time Format
    |--------------------------------------------------------------------------
    |
    | This date and time format is used to display dates and times througout
    | the application. Be aware that changing this, does not modify existing
    | records, but may change the precision, with which existing records are
    | displayed. It also does not limit the precision with which future records
    | are stored.
    |
    */
    'date_format' => 'd.m.Y',
    'time_format' => 'H:i',

    'view_past_months' => 2,

    'store_threshold' => CarbonInterval::month(),

    'modify_threshold' => CarbonInterval::week(),

    'min_date' => Carbon::create(1970)->firstOfYear(),
    'max_date' => Carbon::create(2037)->endOfYear(),

];
