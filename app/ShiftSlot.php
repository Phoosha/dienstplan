<?php

namespace App;


use Auth;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * A <code>ShiftSlot</code> links a <code>Shift</code> and a <code>Slot</code>
 * with a <code>Collection</code> of <code>Duty</code>s that belong to both.
 *
 * @package App
 *
 * @property-read Shift $shift
 * @property-read Slot $slot
 * @property-read Collection $duties
 */
class ShiftSlot {

    protected $shift;
    protected $slot;
    protected $duties;

    /**
     * Creates an empty <code>ShiftSlot</code>.
     *
     * @param Shift $shift
     * @param Slot $slot
     */
    public function __construct(Shift $shift, Slot $slot) {
        $this->shift  = $shift;
        $this->slot   = $slot;
        $this->duties = new Collection();
    }

    /**
     * Syntactic sugar for the constructor {@see ShiftSlot::__construct()}.
     *
     * @param Shift $shift
     * @param Slot $slot
     * @return static
     */
    public static function create(Shift $shift, Slot $slot) {
        return new static($shift, $slot);
    }

    public function __get($name) {
        switch ($name) {
            case 'shift':
                return $this->shift;
            case 'slot':
                return $this->slot;
            case 'duties':
                return $this->duties;
            default:
                throw new InvalidArgumentException("Unknown getter '{$name}'");
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
     * Binds the <i>selection</i> from <code>$duties</code> to this instance
     * that belong to its <code>Shift</code> and <code>Slot</code>.
     *
     * @param Collection $duties
     * @return $this
     */
    public function setDuties(Collection $duties) {
        $this->duties = $duties->filter(function ($duty) {
            return $duty->slot_id === $this->slot->id && (
                ( $duty->start >= $this->shift->start && $duty->start <  $this->shift->end ) ||
                ( $duty->end   >  $this->shift->start && $duty->end   <= $this->shift->end ) ||
                ( $duty->start <= $this->shift->start && $duty->end   >= $this->shift->end )
            );
        });

        return $this;
    }

    /**
     * Checks whether <code>$duties</code> cover this <code>ShiftSlot</code>
     * at most <code>$times</code> redundantly.
     *
     * @param int $times
     * @param Collection $duties
     * @return int actual coverage between 0 and <code>$times</code>
     */
    private function analyzeCoverage(int $times, Collection $duties) {
        $offsets = array_fill(0, $times, $this->shift->start);
        $target  = $this->shift->end;

        $duties->sortBy('start');

        /*
         * Using the the next smallest Duty replace the smallest time
         * in $offsets if we can reach further using that Duty and
         * otherwise drop that offset.
         */
        foreach ($duties as $duty) {
            if (empty($offsets) || $offsets[0] >= $target)
                break;

            while ($smallest_offset = array_shift($offsets)) {
                if ($duty->start <= $smallest_offset) {
                    $offsets[] = $duty->end;
                    break;
                }
            }

            sort($offsets);
        }

        // Now remove all offsets which did not reach the shift end
        while (! empty($offsets) && $offsets[0] < $target) {
            array_shift($offsets);
        }

        return count($offsets);
    }

    /**
     * Returns the overall coverage of this instance.
     *
     * @return int between 0 and 2
     */
    public function getCoverage() {
        return $this->analyzeCoverage(2, $this->duties);
    }

    /**
     * Checks whether this instance should be selectable for the currently
     * logged in <code>User</code>.
     *
     * @return bool
     */
    public function isSelectable() {
        $serviceCoverage = $this->analyzeCoverage(
            1,
            $this->duties->where('type', Duty::SERVICE));
        $meCoverage = $this->analyzeCoverage(
            1,
            $this->duties->where('user_id', Auth::user()->id)
        );

        return $serviceCoverage === 0 && $meCoverage === 0;
    }

    /**
     * Helper returning a list of classes that apply to the instance.
     *
     * @return string space-separated classes of the instance
     */
    public function classes() {
        $classes = [];

        switch ($this->getCoverage()) {
            case 0:
                $classes[] = 'empty-slot';
                break;
            case 1:
                $classes[] = 'alone-slot';
                break;
        }

        if ($this->isSelectable())
            $classes[] = 'selectable';

        return implode(' ', $classes);
    }

}