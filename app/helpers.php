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
