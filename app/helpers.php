<?php

use Illuminate\Support\Carbon;

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
 * Outputs value and selection state for an HTML option tag.
 *
 * @param mixed $sel value of the selected option
 * @param mixed $cur value name of the current option
 * @return string
 */
function option($sel, $cur) {
    $selected = $cur === $sel ? ' selected' : '';
    return "value=\"{$cur}\"{$selected}";
}

/**
 * Checks for a range supported by MySQL TIMESTAMP type.
 *
 * @param \Carbon\Carbon $dt
 * @return bool
 */
function hasValidYear(\Carbon\Carbon $dt) {
    return $dt->year >= 1970 && $dt->year < 2038;
}
