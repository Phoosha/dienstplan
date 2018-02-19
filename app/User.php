<?php

namespace App;

use App\Notifications\CompleteRegistration;
use App\Notifications\ResetPassword;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
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
 * @property string $register_token
 * @property string $remember_token
 * @property boolean $is_admin
 * @property \Carbon\Carbon $last_training
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
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = [
        'last_training', 'created_at', 'updated_at', 'deleted_at',
    ];

    /**
     * Mutator for the <code>password</code> field, that invalidates the
     * <code>register_token</code> whenever the password is set to non-empty.
     *
     * @param $value
     */
    public function setPasswordAttribute($value) {
        if (! empty($value))
            $this->register_token = null;

        $this->attributes['password'] = $value;
    }

    /**
     * Returns the full (first + last) name of the <code>User</code>.
     *
     * @return string
     */
    public function getFullName() {
        return "{$this->first_name} {$this->last_name}";
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
     * Send the password reset notification.
     *
     * @param  string $token
     * @return void
     */
    public function sendPasswordResetNotification($token) {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Send a notification to the user after registration.
     *
     * @param $token
     * @return void
     */
    public function sendRegisterNotification($token) {
        $this->notify(new CompleteRegistration($token));
    }

    /**
     * Sets and stores a new <code>api_token</code> for this <code>User</code>.
     */
    public function cycleApiToken() {
        $this->api_token = str_random(60);
        $result = DB::table('users')
            ->where('id', $this->id)
            ->update([ 'api_token' => $this->api_token]);
        if ($result > 0)
            $this->syncOriginalAttribute('api_token');
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
     * Returns a human readable string representation of the
     * <code>last_training</code> field.
     *
     * @param Carbon|null|false $other    if <code>false</code> absolute time,
     *                                    otherwise {@see Carbon::diffForHumans()}
     * @param bool              $absolute removes time difference modifiers ago,
     *                                    after, etc
     * @param bool              $short    displays short format of time units
     *
     * @return string
     */
    public function getLastTrainingForHumans($other = false, $absolute = false, $short = false) {
        if (empty($this->last_training))
            return 'nie';
        elseif ($other !== false)
            return $this->last_training->isSameDay($other ?? now())
                ? 'heute'
                : $this->last_training->diffForHumans($other, $absolute, $short);
        else
            return $this->last_training
                ->format(config('dienstplan.date_format'));
    }

    /**
     * Helper returning a list of classes that apply to the <code>$last_training</code> attribute.
     *
     * @return string space-separated classes of the instance
     */
    public function lastTrainingClasses() {
        $threshold_dt = now()->sub(config('dienstplan.expiry.user_training'));

        if (empty($this->last_training) || $this->last_training->lt($threshold_dt))
            return 'overdue';

        return '';
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
