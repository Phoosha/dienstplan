<?php

namespace App;

use Carbon\Carbon;
use InvalidArgumentException;

/**
 * A <code>Shift</code> constituting an every 24h recurring time slot.
 *
 * A <code>Shift</code> is never persisted by itself but instead becomes a
 * <code>Duty</code> once a <code>User</code> takes it. Every <code>Shift</code>
 * has start and end datetime and is directly followed and preceded by another
 * shift, which possibly belongs to another day.
 *
 * A <code>Shift</code> is said to belong to a day if its start datetime
 * belongs to that day. Therefore the properties of the <code>Carbon</code>
 * object describing the start datetime are directly available as properties
 * of a <code>Shift</code>.
 *
 * Furthermore <code>Shift</code>s may be bound to a <code>$slot</code>
 * such as a vehicle or a station.
 *
 * @package App
 *
 * @property-read int $shift recurring identifier of the shift
 * @property-read \Carbon\Carbon $start
 * @property-read \Carbon\Carbon $end
 * @property int $slot
 *
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

    /**
     * Configures available shifts.
     *
     * @return array
     */
    private static function shifts() {
        return config('dienstplan.shifts');
    }

    /**
     * Creates a <code>Shift</code> encompassing <code>$dt</code>.
     *
     * @param \Carbon\Carbon|\DateTime $dt if <code>null</code>, the current time is used
     */
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

    /**
     * Creates the first <code>Shift</code> of the day of <code>$dt</code>.
     *
     * @param \Carbon\Carbon|\DateTime $dt
     * @return Shift
     */
    public static function firstOfDay($dt = null) {
        $dt = Carbon::instance($dt ?? now());
        $firstStartTime = Shift::shifts()[0];

        return new Shift($dt->copy()->setTimeFromTimeString($firstStartTime));
    }


    /**
     * Creates the last <code>Shift</code> of the day of <code>$dt</code>.
     *
     * @param \Carbon\Carbon|\DateTime $dt
     * @return Shift
     */
    public static function lastOfDay($dt = null) {
        $dt = Carbon::instance($dt ?? now());
        $lastStartTime = array_last(Shift::shifts());

        return new Shift($dt->copy()->setTimeFromTimeString($lastStartTime));
    }

    /**
     * Creates the <code>Shift</code> given by <code>$year</code>,
     * <code>$month</code>, <code>$day</<code> and the shift identifier
     * <code>$shift</code>.
     *
     * If any of the parameters is <code>null</code> the current time
     * and date are used instead.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $shift
     * @return Shift
     */
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

    /**
     * Returns the number of shifts per day resp. 24h.
     *
     * @return int
     */
    public static function shiftsPerDay() {
        return count(Shift::shifts());
    }

    /**
     * Clones this <code>Shift</code>.
     *
     * @return Shift
     */
    public function copy() {
       return clone $this;
    }

    /**
     * Modifies this <code>Shift</code> to represent the next one.
     *
     * @return $this
     */
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

    /**
     * Modifies this <code>Shift</code> to represent the previous one.
     *
     * @return $this
     */
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

    /**
     * Determines if the instance is the first shift on its day.
     *
     * @return bool
     */
    public function isFirstShift() {
        return $this->shift === 0;
    }

    /**
     * Determines if the instance is the last shift on its day.
     *
     * @return bool
     */
    public function isLastShift() {
        return $this->shift === Shift::shiftsPerDay() - 1;
    }

    /**
     * Determines if the last shift on the same day as this instance is past.
     *
     * So either all <code>Shift</code>s of a day are past or none.
     *
     * @return bool
     */
    public function isPast() {
        return Shift::lastOfDay($this->start)
            ->end->lt(config('dienstplan.past_threshold'));
    }

    /**
     * Determines if the instance is in the future.
     *
     * @return bool
     */
    public function isFuture() {
        return $this->start->gt(now());
    }

    /**
     * Determines if the instance is now.
     *
     * So the instance encompasses the current time.
     *
     * @return bool
     */
    public function isNow() {
        return ! $this->isFuture() && $this->end->lt(now());
    }

    /**
     * Determines if the instance is today and not in the future.
     *
     * @return bool
     */
    public function isNowish() {
        return ! $this->isFuture() && ! $this->isPast();
    }

    /**
     * Determines if the instance is the first shift today and not in the future.
     *
     * @return bool
     */
    public function isFirstNowish() {
        return $this->isNowish() && $this->copy()->prev()->isPast();
    }

    /**
     * Returns a human readable name for the instance.
     *
     * @return string
     */
    public function name() {
        return Shift::shifts()[$this->shift] . ' Uhr';
    }

    /**
     * Helper returning a list of classes that apply to the instance.
     *
     * @return string space-separated classes of the instance
     */
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

    /**
     * Returns a <code>Duty</code> built from this instance.
     *
     * @return Duty
     */
    public function toDuty() {
        return (new Duty())->fill([
            'start' => $this->start,
            'end'   => $this->end,
        ]);
    }

}
