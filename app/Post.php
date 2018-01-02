<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
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
