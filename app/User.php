<?php

namespace App;

use App\Notifications\ResetPassword;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * A <code>User</code> of the application.
 *
 * @package App
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $login
 * @property string $email
 * @property string $phone
 * @property string $password
 * @property string $api_token
 * @property string $remember_token
 * @property boolean $is_admin
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 *
 * @property Collection $posts
 * @property Collection $duties
 */
class User extends Authenticatable {

    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'login', 'email', 'phone',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'api_token', 'remember_token',
    ];

    /**
     * Returns the full (first + last) name of the <code>User</code>.
     *
     * @return string
     */
    public function getFullName() {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Send the password reset notification.
     *
     * @param  string $token
     * @return void
     */
    public function sendPasswordResetNotification($token) {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Sets and stores a new <code>api_token</code> for this <code>User</code>.
     */
    public function cycleApiToken() {
        $this->api_token = str_random(60);
        $result = DB::table('users')
            ->where('id', $this->id)
            ->update([ 'api_token' => $this->api_token]);
        $this->syncOriginalAttribute('api_token');
    }

    /**
     * Returns an API URL to the iCalendar of the <code>User</code>.
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getCalendarURL() {
        if (empty($this->api_token))
            $this->cycleApiToken();

        return url("api/ics/duties?api_token={$this->api_token}");
    }

    /**
     * The natural ordering of <code>User</code>s.
     *
     * @param $query
     * @return Builder
     */
    public function scopeOrdering($query) {
        return $query->orderBy('last_name')
            ->orderBy('first_name')
            ->orderBy('created_at');
    }

    /**
     * Get the <code>Post</code>s made by that <code>User</code>
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts() {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the <code>Duty</code>s taken by that <code>User</code>
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function duties() {
        return $this->hasMany(Duty::class);
    }

}
