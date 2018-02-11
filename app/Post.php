<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * A <code>Post</code> to a bulletin board done by a <code>User</code>.
 *
 * @package App
 *
 * @property int $id
 * @property int user_id
 * @property string $title
 * @property string $body
 * @property \Carbon\Carbon $release_on
 * @property \Carbon\Carbon $expire_on
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property User $user
 */
class Post extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'body', 'release_on', 'expire_on'
    ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = [
        'release_on', 'expire_on', 'created_at', 'updated_at'
    ];

    /**
     * The natural ordering of <code>Post</code>s.
     *
     * @param $query
     * @return Builder
     */
    public function scopeOrdering($query) {
        return $query->orderBy('release_on', 'DESC')
            ->orderByRaw('case when expire_on is null then 1 else 0 end')
            ->orderBy('expire_on')
            ->orderBy('created_at');
    }

    /**
     * Selects all <code>Post</code>s that have expired.
     *
     * @param $query
     * @param \Carbon\Carbon|\DateTime $now
     * @param bool $not if <code>true</code> inverts the scope
     * @return Builder
     */
    public function scopeExpired($query, $now = null, $not = false) {
        $now = Carbon::instance($now ?? now());
        return $not
            ? $query->whereNull('expire_on')->orWhere('expire_on', '>', $now)
            : $query->whereNotNull('expire_on')->where('expire_on', '<=', $now);
    }

    /**
     * Selects all <code>Post</code>s that have not expired.
     *
     * @param $query
     * @param \Carbon\Carbon|\DateTime $now
     * @return Builder
     */
    public function scopeNotExpired($query, $now = null) {
        return $this->scopeExpired($query, $now, true);
    }

    /**
     * Selects all active (released and not expired) <code>Post</code>s.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query) {
        return $query->notExpired()->where('release_on', '<=', now());
    }

    /**
     * Get the <code>User</code> that made the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

}
