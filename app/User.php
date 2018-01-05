<?php

namespace App;

use App\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Collection;
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
 * @property string remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property mixed $posts
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'login', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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
     * Get the posts made by that <code>User</code>
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts() {
        return $this->hasMany(Post::class);
    }

}
