<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public static function active() {
        return static::where('release_on', '<=', now())
            ->where(function ($query) {
                $query->whereNull('expire_on')
                      ->orWhere('expire_on', '>', now());
            })
            ->get();
    }
}
