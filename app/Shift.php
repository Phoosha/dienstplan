<?php

namespace App;

use Carbon\Carbon;

/**
 * @property-read int $shift
 * @property-read \Carbon\Carbon $start
 * @property-read \Carbon\Carbon $end
 * @property-read int $year
 * @property-read int $yearIso
 * @property-read int $month
 * @property-read int $day
 * @property-read int $hour
 * @property-read int $minute
 * @property-read int $second
 * @property-read int $timestamp seconds since the Unix Epoch
 * @property-read \DateTimeZone $timezone the current timezone
 * @property-read \DateTimeZone $tz alias of timezone
 * @property-read int $micro
 * @property-read int $dayOfWeek 0 (for Sunday) through 6 (for Saturday)
 * @property-read int $dayOfYear 0 through 365
 * @property-read int $weekOfMonth 1 through 5
 * @property-read int $weekOfYear ISO-8601 week number of year, weeks starting on Monday
 * @property-read int $daysInMonth number of days in the given month
 * @property-read int $age does a diffInYears() with default parameters
 * @property-read int $quarter the quarter of this instance, 1 - 4
 * @property-read int $offset the timezone offset in seconds from UTC
 * @property-read int $offsetHours the timezone offset in hours from UTC
 * @property-read bool $dst daylight savings time indicator, true if DST, false otherwise
 * @property-read bool $local checks if the timezone is local, true if local, false otherwise
 * @property-read bool $utc checks if the timezone is UTC, true if UTC, false otherwise
 * @property-read string $timezoneName
 * @property-read string $tzName
 */
class Shift {

    protected $shift;
    protected $start;
    protected $end;
    public $slot;

    public function __construct($dt = null) {
        $dt = Carbon::instance($dt ?? now());

        // start time of the first shift on the day of $dt
        $first_start = $dt->copy()->setTimeFromTimeString(Shift::shifts()[0]);

        // check if the shift start the day before $dt
        if ($dt->lt($first_start)) {

            $this->start = $dt->copy()
                ->subDay()
                ->setTimeFromTimeString(array_last(Shift::shifts()));
            $this->end   = $first_start;
            $this->shift = Shift::shiftsPerDay() - 1;

        // otherwise the shift starts the same day as $dt
        } else {

            // remember the previous shift as start time, so initially the first shift
            $start = $first_start;

            // iterate the remaining shifts as end times
            for ($i = 1; $i < Shift::shiftsPerDay(); $i++) {

                $end = $dt->copy()->setTimeFromTimeString(Shift::shifts()[$i]);

                if ($dt->lt($end)) {
                    $this->start = $start;
                    $this->end   = $end;
                    $this->shift = $i - 1;
                    return;
                }

                $start = $end;

            }

            // if we reach here, the shift starts the day after $dt
            $this->start = $start;
            $this->end   = $dt->copy()
                ->addDay()
                ->setTimeFromTimeString(Shift::shifts()[0]);
            $this->shift = Shift::shiftsPerDay() - 1;
        }
    }

    private static function shifts() {
        return config('dienstplan.shifts');
    }

    public static function firstOfDay($dt = null) {
        $dt = Carbon::instance($dt ?? now());
        $firstStartTime = Shift::shifts()[0];

        return new Shift($dt->copy()->setTimeFromTimeString($firstStartTime));
    }

    public static function lastOfDay($dt = null) {
        $dt = Carbon::instance($dt ?? now());
        $lastStartTime = array_last(Shift::shifts());

        return new Shift($dt->copy()->setTimeFromTimeString($lastStartTime));
    }

    public static function create($year = null, $month = null, $day = null, $shift = null) {
        $dt = Carbon::createFromDate($year, $month, $day);

        if (! isset($shift))
            return new Shift($dt);

        $shiftStartTime = Shift::shifts()[
            min(
                max(0,
                    $shift),
                Shift::shiftsPerDay() - 1)
        ];

        return new Shift($dt->setTimeFromTimeString($shiftStartTime));
    }

    public function copy() {
       return clone $this;
    }

    public function next() {
        $this->shift = ($this->shift + 1) % Shift::shiftsPerDay();
        $this->start = $this->end;

        if ($this->shift === Shift::shiftsPerDay() - 1) {
            $this->end = $this->start
                ->copy()
                ->addDay()
                ->setTimeFromTimeString(Shift::shifts()[0]);
        } else {
            $this->end = $this->start
                ->copy()
                ->setTimeFromTimeString(Shift::shifts()[$this->shift + 1]);
        }

        return $this;
    }

    public function prev() {
        $this->shift = $this->shift - 1;
        $this->end = $this->start;

        if ($this->shift < 0) {
            $this->shift = Shift::shiftsPerDay() - 1;
            $this->start = $this->end
                ->copy()
                ->subDay()
                ->setTimeFromTimeString(array_last(Shift::shifts()));
        } else {
            $this->start = $this->end
                ->copy()
                ->setTimeFromTimeString(Shift::shifts()[$this->shift]);
        }

        return $this;
    }

    public function setSlot($slot) {
        $this->slot = $slot;

        return $this;
    }

    public function __get($name) {
        switch ($name) {
            case 'start':
                return $this->start;
            case 'end':
                return $this->end;
            case 'shift':
                return $this->shift;
            default:
                return $this->start->__get($name);
        }
    }

    public function __isset($name) {
        try {
            $this->__get($name);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

    public static function shiftsPerDay() {
        return count(Shift::shifts());
    }

    public function isFirstShift() {
        return $this->shift === 0;
    }

    public function isLastShift() {
        return $this->shift === Shift::shiftsPerDay() - 1;
    }

    public function isPast() {
        return Shift::lastOfDay($this->start)
            ->end->lt(config('dienstplan.past_threshold'));
    }

    public function isFuture() {
        return $this->start->gt(now());
    }

    public function isNow() {
        return ! $this->isFuture() && $this->end->lt(now());
    }

    public function isNowish() {
        return ! $this->isFuture() && ! $this->isPast();
    }

    public function isFirstNowish() {
        return $this->isNowish() && $this->copy()->prev()->isPast();
    }

    public function name() {
        return Shift::shifts()[$this->shift] . ' Uhr';
    }

    public function classes() {
        $classes = [];
        if ($this->start->isWeekday())
            $classes[] = 'weekday';
        if ($this->start->isWeekend())
            $classes[] = 'weekend';
        if ($this->start->isToday())
            $classes[] = 'today';
        if ($this->isPast())
            $classes[] = 'past';

        return implode(" ", $classes);
    }

}
