<?php

namespace App\Providers;

use App\Auth\RegisterTokenGuard;
use App\CalendarMonth;
use App\Duty;
use App\Phone;
use App\Policies\DutyPolicy;
use App\Policies\PhonePolicy;
use App\Policies\PostPolicy;
use App\Policies\UserPolicy;
use App\Post;
use App\Shift;
use App\User;
use Auth;
use Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider {

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Duty::class => DutyPolicy::class,
        User::class => UserPolicy::class,
        Phone::class => PhonePolicy::class,
        Post::class => PostPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot() {
        $this->registerPolicies();

        Auth::extend('register_token', function ($app, $name, array $config) {
            return new RegisterTokenGuard(
                Auth::createUserProvider($config['provider']),
                $app['request']
            );
        });

        Gate::define('month.view', function (User $user, CalendarMonth $month) {
            $first_shift = Shift::firstOfDay($month->start);
            return $user->can('view', $first_shift->toDuty());
        });
        Gate::define('administrate', function (User $user) {
            return $user->can('administrate', User::class);
        });
    }

}
