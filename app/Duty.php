<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
 * @property \Carbon\Carbon $start
 * @property \Carbon\Carbon $end
 * @property int $slot
 * @property string $type <code>null</code> unless, the instance is of a special type
 * @property string $comment
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Duty extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start', 'end', 'slot', 'type', 'comment'
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
     * Tries to merge the instance with <code>$duty</code>.
     *
     * @param Duty $duty
     * @return bool <code>true</code> if merging was possible
     */
    public function merge(Duty $duty) {
        if ($this->slot !== $duty->slot)
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
     * Returns the list of <code>User</code>s, which are able to take that <code>Duty</code>.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function possibleTakers() {
       return User::all();
    }

}
