<?php


use Carbon\Carbon;

function dayname_short($dt) {
    return __('date.' . $dt->format('D'));
}

function dayname($dt) {
    return __('date.' . $dt->format('l'));
}

function monthname($dt) {
    return __('date.' . $dt->format('F'));
}

function time_dropdown($dt) {
    $dt  = Carbon::instance($dt)->startOfDay();
    $int = config('dienstplan.dropdown_time_steps');

    $vals = [];
    for ($i = $dt->copy(); $i->isSameDay($dt); $i->add($int))
        $vals[] = $i->copy();

    return $vals;
}

/**
 * Returns a time string that uses minutes and seconds only if necessary.
 *
 * @param Carbon $dt
 * @return string
 */
function minTime(Carbon $dt) {
    $format = 'G:i:s';
    if ($dt->second === 0) {
        if ($dt->minute === 0)
            $format = 'G';
        else
            $format = 'G:i';
    }
    return $dt->format($format);
}

/**
 * Outputs value and selection state for an HTML option tag.
 *
 * @param mixed $sel value of the selected option
 * @param mixed $cur value name of the current option
 * @return string
 */
function selected($sel, $cur) {
    $selected = $cur === $sel ? ' selected' : '';
    return "value=\"{$cur}\"{$selected}";
}

/**
 * Outputs value and selection state for an HTML checkbox.
 *
 * @param mixed $sel value of the checked option
 * @param mixed $cur value name of the current option
 * @return string
 */
function checked($sel, $cur) {
    $selected = $cur === $sel ? ' checked' : '';
    return "value=\"{$cur}\"{$selected}";
}

/**
 * Checks for a range supported by MySQL TIMESTAMP type.
 *
 * @param Carbon $dt
 * @return bool
 */
function hasValidYear(Carbon $dt) {
    return $dt->year >= 1970 && $dt->year < 2038;
}

/**
 * Returns two-dimensional calendar-like array for the month
 * given by <code>$month_start</code>.
 *
 * Every entry of the returned array represents a week and
 * contains <code>Carbon</code> instances for each day of
 * the week.
 *
 * @param Carbon $month_start
 * @return Carbon[][]
 */
function calendar(Carbon $month_start) {
    $month_start = $month_start->firstOfMonth();

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

    return $weeks;
}

/**
 * Safely get a <code>Carbon</code> instance for the first day of the
 * month from <code>$year</code> and <code>$month</code> as integers.
 *
 * Throws a <code>HttpException</code> if <code>$year</code> or
 * <code>$month</code> are invalid.
 *
 * @param $year
 * @param $month
 * @return Carbon
 * @throws HttpException
 */
function firstOfMonth($year, $month) {
    $year  = isset($year)  ? (int) $year  : $year;
    $month = isset($month) ? (int) $month : $month;

    try {
        $month_start = Carbon::createSafe($year, $month, 1, 0);
        if (! hasValidYear($month_start))
            abort(404);
        return $month_start;
    } catch (InvalidArgumentException $e) {
        abort(400, $e->getMessage());
    }
}

