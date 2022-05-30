<?php

namespace App\Console;

use App\Jobs\ProcessResult;
use App\Jobs\RenewTestPoint;
use App\Models\TwoDigit;
use App\Models\TwoDigitHit;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            if (TwoDigitHit::checkDay())
                TwoDigit::getResult();
        })->cron("40 05 * * *");

        $schedule->call(function () {
            if (TwoDigitHit::checkDay())
                TwoDigit::getResult();
        })->cron("10 10 * * *");

        $schedule->call(function () {
            foreach (User::whereIn('id', DB::table('point_user')->where('point_id', 1)->where('balance', '<', 100)->pluck('user_id')->toArray())->get() as $user) {
                RenewTestPoint::dispatch($user);
            }
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
