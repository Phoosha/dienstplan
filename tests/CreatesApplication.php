<?php

namespace Tests;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication {

    /**
     * Returns the shifts that will be configured by {@see createApplication}.
     *
     * @return array
     */
    protected function shifts() {
        return [ '8', '12', '18' ];
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication() {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        Hash::driver('bcrypt')->setRounds(4);

        Config::set('dienstplan.shifts', $this->shifts());
        Config::set('dienstplan.past_threshold', CarbonInterval::hours(10));

        Carbon::setTestNow(
            Carbon::create(
                null, null, 10,
                11, 12, 13
            )
        );

        return $app;
    }
}
