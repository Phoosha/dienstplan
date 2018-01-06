<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property mixed $slot_config
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

}
