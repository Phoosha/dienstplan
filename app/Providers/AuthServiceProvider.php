<?php

namespace App\Providers;

use App\CalendarMonth;
use App\Duty;
use App\Policies\DutyPolicy;
use App\Policies\UserPolicy;
use App\Shift;
use App\User;
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
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot() {
        $this->registerPolicies();

        Gate::define('month.view', function (User $user, CalendarMonth $month) {
            $first_shift = Shift::firstOfDay($month->start);
            return $user->can('view', $first_shift->toDuty());
        });
    }

}
