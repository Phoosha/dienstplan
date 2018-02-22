<?php

namespace App\Providers;

use App\Events\DutyCreated;
use App\Events\DutyDeleted;
use App\Events\DutyReassigned;
use App\Events\DutyUpdated;
use App\Listeners\DispatchDutyNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        DutyCreated::class => [
            DispatchDutyNotification::class,
        ],
        DutyUpdated::class => [
            DispatchDutyNotification::class,
        ],
        DutyReassigned::class => [
            DispatchDutyNotification::class,
        ],
        DutyDeleted::class => [
            DispatchDutyNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
