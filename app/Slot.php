<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property SlotConfig $slot_config
 */
class Slot extends Model {

    /**
     * Do not timestamp.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'name' ];

    /**
     * Returns the <code>SlotConfig</code> this <code>Slot</code> belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function slot_config() {
        return $this->belongsTo(SlotConfig::class);
    }

    /**
     * Returns the <code>Slot</code>s that are active at <code>$month_start</code>
     *
     * @param \Carbon\Carbon|DateTime $month_start
     * @return Collection
     * @see SlotConfig::active()
     */
    public static function allActive($month_start = null) {
        $config = SlotConfig::active($month_start);
        return isset($config) ? $config->slots : new Collection();
    }

}
