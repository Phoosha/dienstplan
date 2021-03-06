<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents a phone number entry.
 *
 * @property int $id
 * @property string name
 * @property string phone
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Phone extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone',
    ];

    /**
     * The natural ordering of <code>Phone</code>s.
     *
     * @param $query
     * @return Builder
     */
    public function scopeOrdering($query) {
        return $query->orderBy('name')->orderBy('phone');
    }

}
