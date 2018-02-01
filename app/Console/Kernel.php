<?php

namespace App\Console;

use App\Duty;
use App\Post;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Delete posts expired before the start of yesterday
        $schedule->call(function () {
            Post::expired(Carbon::yesterday())
                ->each(function (Post $post) {
                    $post->delete();
                });
        })->daily();

        // Hard delete trashed duties after a grace period
        $threshold_dt = now()->sub(config('dienstplan.duties_hard_delete_threshold'));
        $schedule->call(function () use ($threshold_dt) {
           Duty::onlyTrashed()->where('deleted_at', '<', $threshold_dt)
               ->each(function (Duty $duty) {
                   $duty->forceDelete();
               });
        })->monthly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
