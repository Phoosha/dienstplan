<?php

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
    | This threshold determines which shifts to consider as past. Any shift
    | that starts before (and not at) this point in time is past.
    | This can be any time string (relative to now), which is parseable by
    | the Carbon library.
    | Additionally, you should ensure that all shifts of one day are either
    | past or not as by setting the time portion to 00:00:00.
    |
    */
    'past_threshold' => 'yesterday'

];
