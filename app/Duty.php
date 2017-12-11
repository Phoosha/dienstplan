<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Duty extends Model {

    protected $fillable = [
        'start',
        'end',
        'slot'
    ];

    protected $dates = [
        'start',
        'end',
        'created_at',
        'updated_at',
    ];

    public static function createFromShifts($shifts) {
        $shifts = array_sort($shifts, 'start');
        $duties = [];

        $last_duty = null;
        while (count($shifts) > 0) {
            $shift = array_shift($shifts);

            if (! isset($last_duty) || ! $last_duty->merge($shift)) {
                $duty = Duty::createFromShift($shift);
                $duties[] = $duty;
                $last_duty = $duty;
            }
        }

        return $duties;
    }

    public static function createFromShift($shift) {
        return new Duty([
            'start' => $shift->start,
            'end'   => $shift->end,
            'slot'  => $shift->slot,
        ]);
    }

    public function merge($dutyLike) {
        if ($this->slot !== $dutyLike->slot)
            return false;

        if ($this->start->eq($dutyLike->end)) {
            $this->start = $dutyLike->start;
            return true;
        } elseif ($this->end->eq($dutyLike->start)) {
            $this->end = $dutyLike->end;
            return true;
        }

        return false;
    }

}
