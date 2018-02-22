<?php

namespace App;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidDateException;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class CalendarMonth
 *
 * @package App
 *
 * @property-read Carbon $start
 * @property-read Carbon $end
 * @property-read Collection $slots
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
class CalendarMonth {

    protected $start;
    protected $end;
    protected $slots;
    protected $weeksAndDays;
    protected $daysAndShifts;

    /**
     * Creates a <code>CalendarMonth</code>.
     *
     * @param mixed $year
     * @param mixed $month
     * @throws InvalidDateException
     */
    public function __construct($year = null, $month = null) {
       $this->start = self::firstOfMonth($year, $month);
       $this->end   = $this->start->copy()->lastOfMonth();
       $this->slots = Slot::allActive($this->start);
    }

    /**
     * Safely get a <code>Carbon</code> instance for the first day of the
     * month from <code>$year</code> and <code>$month</code> as integers.
     *
     * @param $year
     * @param $month
     * @return Carbon
     * @throws InvalidDateException
     */
    protected static function firstOfMonth($year, $month) {
        $year  = isset($year)  ? (int) $year  : $year;
        $month = isset($month) ? (int) $month : $month;

        return Carbon::createSafe($year, $month, 1, 0);
    }

    /**
     * Creates a <code>CalendarMonth</code> for the next month.
     *
     * @return CalendarMonth
     */
    public function next() {
        $next = $this->start->copy()->addMonth();
        return new CalendarMonth($next->year, $next->month);
    }

    /**
     * Creates a <code>CalendarMonth</code> for the previous month.
     *
     * @return CalendarMonth
     */
    public function prev() {
        $prev = $this->start->copy()->subMonth();
        return new CalendarMonth($prev->year, $prev->month);
    }

    /**
     * Returns an array of weeks, each with entries for every day.
     *
     * @return Carbon[][]
     */
    public function getWeeksAndDays() {
        if (! isset($this->weeksAndDays))
            $this->initializeWeeksAndDays();

        return $this->weeksAndDays;
    }

    protected function initializeWeeksAndDays() {
        // the calendars displays whole weeks only
        $cal_start   = $this->start->copy()->startOfWeek();
        $numWeeks    = $this->end->weekOfMonth;

        $weeks = [];
        $day = $cal_start->copy();
        for ($week = 0; $week < $numWeeks; $week++) {
            $weeks[$week] = [];
            for ($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++) {
                $weeks[$week][] = $day;
                $day = $day->copy()->addDay();
            }
        }

        $this->weeksAndDays = $weeks;
    }

    /**
     * Returns an array of days, each with entries for every <code>Shift</code>.
     *
     * @return Shift[][]
     */
    public function getDaysAndShifts() {
        if (! isset($this->daysAndShifts))
            $this->initializeDaysAndShifts();

        return $this->daysAndShifts;
    }

    public function initializeDaysAndShifts() {
        $first_shift = Shift::firstOfDay($this->start);
        $duties = Duty::betweenByDay($this->start, $this->end)->get();

        $days = array_fill(0, $first_shift->daysInMonth, []);
        for ($shift = $first_shift->copy();
             $shift->start->isSameMonth($first_shift->start);
             $shift = $shift->copy()->next()
        ) {
            // drop all past duties
            while ($duties->isNotEmpty() && $duties->first()->end <= $shift->start)
                $duties->shift();

            $days[$shift->day - 1][] = $shift->setShiftSlots($this->slots, $duties);
        }

        $this->daysAndShifts = $days;
    }

    /**
     * Returns whether this month is usable regarding available
     * slots and the timestamp range.
     *
     * @return bool
     */
    public function isUsable() {
        return $this->slots->isNotEmpty()
            && isValidDate($this->start)
            && isValidDate($this->end);
    }

    public function __get($name) {
        switch ($name) {
            case 'start':
                return $this->start->copy();
            case 'end':
                return $this->end->copy();
            case 'slots':
                return $this->slots;
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

}