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
            return $duty->slot_id === $this->slot->id;
        });

        return $this;
    }

    /**
     * Returns <code>1</code> if this <code>ShiftSlot</code> is completely
     * covered by a service duty and <code>0</code> otherwise.
     * @return int
     */
    public function getServiceCoverage(): int {
        return $this->shift->analyzeCoverage(
            1,
            $this->duties->where('type', Duty::SERVICE));
    }

    /**
     * Checks whether this instance should be selectable for the currently
     * logged in <code>User</code>.
     *
     * @return bool
     */
    public function isSelectable() {
        $serviceCoverage = $this->getServiceCoverage();
        $meCoverage = $this->shift->analyzeCoverage(
            1,
            $this->shift->duties
                ->where('user_id', Auth::user()->id)
                ->where('type', '!=', Duty::SERVICE)
        );

        return $serviceCoverage === 0
            && ( $meCoverage === 0 || Auth::user()->can('impersonate', Duty::class))
            && Auth::user()->can('create', $this->shift->toDuty());
    }

    /**
     * Helper returning a list of classes that apply to the instance.
     *
     * @return string space-separated classes of the instance
     */
    public function classes() {
        $classes = [];

        if ($this->getServiceCoverage() === 1)
            $classes[] = 'service-slot';
        else
            switch ($this->shift->getCoverage()) {
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