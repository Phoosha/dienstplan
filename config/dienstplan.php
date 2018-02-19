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
    'past_threshold' => CarbonInterval::hours(10),

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
    | This date and time format is used to display dates and times throughout
    | the application. Be aware that changing this, does not modify existing
    | records, but may change the precision, with which existing records are
    | displayed. It also does not limit the precision with which future records
    | are stored.
    |
    */
    'date_format' => 'd.m.Y',
    'time_format' => 'H:i',
    'datetime_format' => 'd.m.Y H:i',

    /*
    |--------------------------------------------------------------------------
    | Phone Format
    |--------------------------------------------------------------------------
    |
    | This regular expression defines the format for valid phone numbers.
    | By default DIN 5008 is followed, allowing:
    |   112
    |   0176 3822349
    |   08141 283246-34
    |
    */
    'phone_regex' => '/^(0[0-9]+ )?[0-9][0-9][0-9]+(-[0-9]+)*$/',

    /*
    |--------------------------------------------------------------------------
    | Min and Max Date
    |--------------------------------------------------------------------------
    |
    | These are the minimum and maximum date before which, respectively after
    | which, no duties may be stored. This should be chosen according to the
    | date range supported by the configured database application.
    |
    */
    'min_date' => Carbon::create(1970)->firstOfYear(),
    'max_date' => Carbon::create(2037)->endOfYear(),

    /*
    |--------------------------------------------------------------------------
    | Duty Permission Thresholds
    |--------------------------------------------------------------------------
    |
    | This section contains various thresholds, which limit the permissions of
    | normal users (not admin users) to interact with duties based on the
    | distance of a duty to a point in time. This point in time is in here
    | either referred to as:
    |   - now()           = time of the request (processing)
    |   - now_shift_end() = end of the shift at now()
    |
    | Every threshold configuration contains an expression-like formulation of
    | the criterion for >granting< that permission.
    |
    */
    'duty' => [
        /*
        |--------------------------------------------------------------------------
        | Visibility of Past Duties
        |--------------------------------------------------------------------------
        |
        | A normal user may only view duties whose end is no older than this
        | CarbonInterval. This should currently be set in WHOLE MONTHS.
        |
        |   end > now() - view_past --> grants: view
        |
        */
        'view_past' => CarbonInterval::months(2),

        /*
        |--------------------------------------------------------------------------
        | Storage of Future Duties
        |--------------------------------------------------------------------------
        |
        | A normal user may only store a new duty whose start is no more in the
        | future relative to the end of the now shift than this CarbonInterval.
        |
        |   end <= now_shift_end() + store_future --> grants: store
        |
        */
        'store_future' => CarbonInterval::month(),

        /*
        |--------------------------------------------------------------------------
        | Modification of Duties
        |--------------------------------------------------------------------------
        |
        | A normal user may only update duties whose start is no less in the
        | future relative to the end of the now shift than this CarbonInterval.
        |
        |   start >= now_shift_end() + modify --> grants: modify
        |
        */
        'modify' => CarbonInterval::week(),

        /*
        |--------------------------------------------------------------------------
        | Modification of Recently Created Duties
        |--------------------------------------------------------------------------
        |
        | A normal user may update duties as long as he may have still created
        | an identical duty (s. store_future) and as long as the duty is no
        | older than this CarbonInterval.
        |
        |   created_at > now() - modify_grace --> grants: modify
        |
        | This takes precedence over the modify threshold.
        |
        */
        'modify_grace' => CarbonInterval::minutes(5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Expiry Thresholds
    |--------------------------------------------------------------------------
    |
    | This section contains various thresholds, configured as CarbonInterval,
    | which determine when an object or information expires.
    |
    */
    'expiry' => [
        /*
        |--------------------------------------------------------------------------
        | Users' Last Training
        |--------------------------------------------------------------------------
        |
        | The last_training property of a User is said to be expired when older
        | than this CarbonInterval.
        |
        */
        'user_training' => CarbonInterval::months(6),

        /*
        |--------------------------------------------------------------------------
        | Registration Invitation
        |--------------------------------------------------------------------------
        |
        | For security reasons the register_token provided with a registration
        | invitation expires when the register_token is older than this
        | CarbonInterval. In this case (upon registration attempt) a new
        | registration invitation with a new register_token is mailed to the
        | User.
        |
        */
        'user_register_token' => CarbonInterval::week(),

        /*
        |--------------------------------------------------------------------------
        | Soft Deleted Duties
        |--------------------------------------------------------------------------
        |
        | A soft deleted duty whose deletion is older than this CarbonInterval
        | may be (and will be when scheduled tasks are run by a cronjob) fully
        | and irrevocably deleted.
        |
        */
        'duty_soft_deleted' => CarbonInterval::month(),
    ],

    /*
    |--------------------------------------------------------------------------
    | Duty Notification Delay
    |--------------------------------------------------------------------------
    |
    | A notification mail about a DutyEvent except DutyCreated is delayed by
    | this CarbonInterval, which gives some time to correct erroneous
    | modifications. However, even if the duty is returned to its original state
    | a notification is sent anyway.
    |
    */
    'duty_notification_delay' => CarbonInterval::minute(),

];
