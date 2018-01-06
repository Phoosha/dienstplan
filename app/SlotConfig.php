<?php

namespace App;

use Carbon\Carbon;
use Faker\Provider\DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 *
 *
 * @property int $id
 * @property \Carbon\Carbon $available_on
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property Slot[] $slots
 */
class SlotConfig extends Model {


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'available_on' ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = [
        'available_on', 'created_at', 'updated_at'
    ];

    /**
     * Creates and persists a <code>SlotConfig</code> including its associated <code>Slot</code>s.
     *
     * @param Carbon $available_on
     * @param string[] $slotNames
     * @return SlotConfig
     */
    public static function createWithSlots(Carbon $available_on, array $slotNames) {
        $config = SlotConfig::create(compact('available_on'));
        $config->slots()->createMany(
            array_map(function($name) {
                return [ 'name' => $name ];
            }, $slotNames)
        );
        return $config;
    }

    /**
     * Sets the <code>available_on</code> attribute always to the first of the month.
     *
     * @param Carbon $date
     */
    public function setAvailableOnAttribute(Carbon $date) {
        $this->attributes['available_on'] = $date->firstOfMonth();
    }

    /**
     * Get the <code>Slot</code>s that belong to this <code>SlotConfig</code>
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function slots() {
        return $this->hasMany(Slot::class);
    }

    /**
     * @param \Carbon\Carbon|DateTime $month_start
     * @return SlotConfig
     */
    public static function active($month_start = null) {
        $month_start = Carbon::instance($month_start ?? now())->firstOfMonth();

        return static::where('available_on', '<=', $month_start)
            ->orderBy('available_on', 'desc')
            ->first();
    }

    /**
     * @param \Carbon\Carbon|DateTime $month_start
     * @return SlotConfig
     * @throws ModelNotFoundException
     */
    public static function activeOrFail($month_start = null) {
        return self::active($month_start)->firstOrFail();
    }

}