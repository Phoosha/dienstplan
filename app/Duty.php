<?php

namespace App;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Webpatser\Uuid\Uuid;

/**
 * A <code>Duty</code> taken by a <code>User</code> for a specific <code>$slot</code>.
 *
 * A <code>Duty</code> is typically expected to have (roughly) matching start and
 * end datetime with some <code>Shift</code>. But this is not a strict requirement.
 *
 * @package App
 *
 * @property int $id
 * @property int $user_id
 * @property User $user
 * @property int $slot_id
 * @property Slot $slot
 * @property \Carbon\Carbon $start
 * @property \Carbon\Carbon $end
 * @property int $type <code>null</code> unless, the instance is of a special type
 * @property string $comment
 * @property int $sequence incremented for every update
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class Duty extends Model {

    use SoftDeletes;

    const NORMAL = 0;
    const WITH_INTERNEE = 1;
    const SERVICE = 2;
    const TYPES = [
        Duty::NORMAL,
        Duty::WITH_INTERNEE,
        Duty::SERVICE
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start', 'end', 'slot_id', 'comment', 'type'
    ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = [
        'start', 'end', 'created_at', 'updated_at'
    ];

    /**
     * Register hooks for <code>Model</code> lifetime events.
     */
    public static function boot() {
        parent::boot();

        // Initialize uuid and sequence
        self::creating(function ($model) {
            $model->uuid = (string) Uuid::generate(4);
            $model->sequence = 0;
        });

        // Increment sequence on every update
        self::updating(function ($model) {
            $model->sequence += 1;
        });
    }

    /**
     * Returns the <code>sequence</code> field or <code>0</code> if uninitialized.
     */
    public function getSequenceAttribute() {
        return $this->attributes['sequence'] ?? 0;
    }

    /**
     * Tries to merge the instance with <code>$duty</code>.
     *
     * @param Duty $duty
     * @return bool <code>true</code> if merging was possible
     */
    public function merge(Duty $duty) {
        static::TYPES[0];
        if ($this->slot_id !== $duty->slot_id)
            return false;

        if ($this->start->eq($duty->end)) {
            $this->start = $duty->start;
            return true;
        } elseif ($this->end->eq($duty->start)) {
            $this->end = $duty->end;
            return true;
        }

        return false;
    }

    /**
     * Returns an array of <code>Duty</code>, where instances have been merged if possible.
     *
     * @param Duty[] $duties
     * @return array
     * @see Duty::merge()
     */
    public static function mergeAll(array $duties) {
        // sorting by start time ensures only one iteration is needed
        $duties    = array_sort($duties, 'start');

        $result    = [];
        $prev_duty = null;
        while (count($duties) > 0) {
            $duty = array_shift($duties);

            if (! isset($prev_duty) || ! $prev_duty->merge($duty)) {
                $result[] = $duty;
                $prev_duty = $duty;
            }
        }

        return $result;
    }

    /**
     * Selects all duties "between" <code>$start</code> and <code>$end</code>.
     *
     * A <code>Duty</code> is said to be between two points in time
     * if it starts or ends inside that time period or encompasses
     * it altogether.
     *
     * If <code>$strict = true</code>, then only duties completely encompassing
     * the interval are selected.
     *
     * @param Builder $query
     * @param Carbon $start
     * @param Carbon $end
     * @param bool $strict
     *
     * @return Builder
     */
    public function scopeBetween(Builder $query, Carbon $start, Carbon $end, bool $strict = false) {
        return $query->where(
            function ($query) use ($start, $end, $strict) { // 1. starts within time period
                if (! $strict)
                    $query->where('start', '>=', $start)->where('start', '<', $end);
            }
        )->orWhere(
            function ($query) use ($start, $end, $strict) { // 2. ends within time period
                if (! $strict)
                    $query->where('end', '>', $start)->where('end', '<=', $end);
            }
        )->orWhere(
            function ($query) use ($start, $end) { // 3. encompasses the time period
                $query->where('start', '<=', $start)->where('end', '>=', $end);
            }
        )->ordering();
    }

    /**
     * Returns a filter closure that takes duties and filters them
     * just like {@see scopeBetween}.
     *
     * @param \Carbon\Carbon $start
     * @param \Carbon\Carbon $end
     * @param bool           $strict
     *
     * @return \Closure
     */
    public static function getBetweenFilter(Carbon $start, Carbon $end, bool $strict = false) {
        return function (Duty $duty) use ($start, $end, $strict) {
            return ( $duty->start >= $start && $duty->start <  $end && ! $strict )
                || ( $duty->end   >  $start && $duty->end   <= $end && ! $strict )
                || ( $duty->start <= $start && $duty->end   >= $end );
        };
    }

    /**
     * Selects all duties "between" the first shift of the day given by
     * <code>$start</code> and the last shift of <code>$end</code>.
     *
     * @param Builder $query
     * @param Carbon $start
     * @param Carbon $end
     * @param bool $strict
     * @return Builder
     * @see Duty::scopeBetween()
     */
    public function scopeBetweenByDay(Builder $query, Carbon $start, Carbon $end, bool $strict = false) {
        $start = Shift::firstOfDay($start)->start;
        $end   = Shift::lastOfDay($end)->end;

        return self::scopeBetween($query, $start, $end, $strict);
    }

    /**
     * Selected duties taken by <code>$user</code>
     *
     * @param Builder $query
     * @param User|int $user
     * @return Builder
     */
    public function scopeTakenBy(Builder $query, $user) {
        if ($user instanceof User)
            $user = $user->id;

        return $query->where('user_id', $user);
    }

    /**
     * The natural ordering of <code>Duty</code>.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return $this
     */
    public function scopeOrdering(Builder $query) {
        return $query
            ->orderBy('start')
            ->orderBy('end', 'desc')
            ->orderBy('created_at');
    }

    /**
     * Returns the list of <code>User</code>s, which are able to take that <code>Duty</code>.
     *
     * @param string $action
     * @return Collection
     */
    public function possibleTakers(string $action = '') {
        if (Auth::user()->can('impersonate', Duty::class)
                && ( empty($action) || Auth::user()->can($action, $this))) {
            $users = User::all();

            // Ensure the current user is always a possible taker
            if (isset($this->user_id) && $this->user->trashed())
                $users->push($this->user);

            return $users;
        } else {
            return Collection::wrap($this->user ?? Auth::user());
        }
    }

    /**
     * Returns the <code>SlotConfig</code> applicable to this <code>Duty</code> based on its <code>start</code> field.
     *
     * @return SlotConfig
     * @throws ModelNotFoundException
     */
    public function applicableSlotConfig() {
        return SlotConfig::activeOrFail($this->start);
    }

    /**
     * Returns the <code>Slot</code>s available to this <code>Duty</code> based on its <code>start</code> field.
     *
     * @param string $action
     * @return Collection
     */
    public function availableSlots(string $action = '') {
        if (Auth::user()->can($action, $this))
            return $this->applicableSlotConfig()->slots;
        else
            return Collection::wrap($this->slot);
    }

    /**
     * Returns the duties from <code>$duties</code> or the DB that are in
     * conflict with this instance.
     *
     * Conflicts are...
     *  - either non-SERVICE duties by the same user for any slot
     *  - or SERVICE duties by any user for the same slot
     *
     * @param Collection|null $duties if <code>null</code>, all duties from the
     *                                database are used
     *
     * @return Collection
     */
    public function getConflicts(Collection $duties = null) {
        if (isset($duties))
            return $duties->filter($this->getConflictsFilter());

        return $this->getConflictsQuery()->get();
    }

    /**
     * Returns a query that selects duties from the database as specified by
     * {@see getConflicts}
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getConflictsQuery() {
        /** @var Builder $query */
        $query = self::between($this->start, $this->end);

        // do not count $this as a conflict
        if (isset($this->id))
            $query->whereKeyNot($this->id);

        if ($this->type == Duty::SERVICE)
            $query->where('slot_id', $this->slot_id);
        else
            $query->where(function ($query) {
                $query
                    ->where(function ($query) { /* ME conflicts */
                        $query->takenBy($this->user_id)->where('type', '<>', Duty::SERVICE);
                    })
                    ->orWhere(function ($query) { /* SERVICE conflicts */
                        $query->where('slot_id', $this->slot_id)->where('type', Duty::SERVICE);
                    });
            });

        return $query;
    }

    /**
     * Returns a filter closure that takes duties and filters them as specified
     * by {@see getConflicts}.
     *
     * @return \Closure
     */
    protected function getConflictsFilter() {
        $between = self::getBetweenFilter($this->start, $this->end);

        if ($this->type == Duty::SERVICE)
            return function (Duty $duty) use ($between) {
                return $between($duty) && $duty->slot_id === $this->slot_id;
            };
        else
            return function (Duty $duty) use ($between) {
                if (! $between($duty) || ( isset($this->id) && $duty->id === $this->id ))
                    return false;
                elseif ($duty->type != Duty::SERVICE)
                    return $duty->user_id === $this->user_id;
                elseif ($duty->type == Duty::SERVICE)
                    return $duty->slot_id === $this->slot_id;

                return false;
            };
    }

    /**
     * Returns a comparison closure that takes two duties and compares them by
     * <code>$fields</code> in that order.
     *
     * This function can be used as a callback to {@see usort} and the like.
     *
     * @param array $fields
     *
     * @return \Closure {@see usort} for semantics
     */
    public static function getCmpBy(array $fields) {
        return function (Duty $a, Duty $b) use ($fields) {
            foreach ($fields as $field) {
                if ($a->{$field} === $b->{$field})
                    continue;

                return $a->{$field} > $b->{$field} ? 1 : -1;
            }

            return 0;
        };
    }

    /**
     * Get the <code>User</code> that has taken this <code>Duty</code>.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get the <code>Slot</code> this <code>Duty</code> belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function slot() {
        return $this->belongsTo(Slot::class);
    }

}
