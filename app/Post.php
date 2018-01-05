<?php

namespace App;

use Carbon\Carbon;
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

    public static function active() {
        return static::where('release_on', '<=', now())
            ->where(function ($query) {
                $query->whereNull('expire_on')
                    ->orWhere('expire_on', '>', now());
            })
            ->get();
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
