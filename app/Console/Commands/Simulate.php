<?php

namespace App\Console\Commands;

use App\Models\TwoDigit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Simulate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simulate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate the program';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // for ($i = 0; $i < 60 * 24 * 60; $i++) {
        //     $days = [0 => 'sun', 1 => 'mon', 2 => 'tue', 3 => 'wed', 4 => 'thur', 5 => 'fri', 6 => 'sat'];
        //     $time = today()->addSeconds($i + (24 * 60 * 60 * 2));
        //     Log::channel('debug')->info($time->format('d-m-Y h:i:s A') . " => " . $days[$time->dayOfWeek]);
        //     Log::channel('debug')->debug((TwoDigit::checkDay($time) && TwoDigit::checkTime(($time))) ? 'allow' : 'limit');
        // }

        DB::transaction(function () {
            DB::statement("INSERT INTO `account_providers` (`id`, `name`, `created_at`, `updated_at`) VALUES
            (1, 'telegram', '2022-05-12 03:51:05', '2022-05-12 03:51:05');");

            DB::statement("INSERT INTO `users` (`id`, `referrer_id`, `name`, `locale`, `banned_at`, `password`, `created_at`, `updated_at`) VALUES
            (2, 1, 'moon2', 'my', NULL, '$2y$10\$r0nHw2yQJzj6ScpSGmsC8e7DUiqL1MkilaN05WQGPLGtdyChKqqui', '2022-05-12 03:51:05', '2022-05-12 03:51:05'),
            (3, 2, 'moon1', 'my', NULL, '$2y$10\$r0nHw2yQJzj6ScpSGmsC8e7DUiqL1MkilaN05WQGPLGtdyChKqqui', '2022-05-12 03:51:12', '2022-05-12 03:51:39');");

            DB::statement("INSERT INTO `account_provider_user` (`id`, `user_id`, `account_provider_id`, `provider_id`, `username`, `sent_at`, `created_at`, `updated_at`) VALUES
            (1, 2, 1, 5144374717, NULL, '2022-05-12 03:51:05', '2022-05-12 03:51:05', '2022-05-12 03:51:05'),
            (2, 3, 1, 1391365941, 'lunablessing', '2022-05-12 03:51:12', '2022-05-12 03:51:12', '2022-05-12 03:51:12');");

            DB::statement("INSERT INTO `top_ups` (`id`, `user_id`, `payment_id`, `status`, `amount`, `created_at`, `updated_at`) VALUES
            (1, 3, 1, 2, 1000, '2022-05-12 03:55:43', '2022-05-12 03:56:20');");

            DB::statement("INSERT INTO `pictures` (`id`, `name`, `pictureable_id`, `pictureable_type`, `created_at`, `updated_at`) VALUES
            (1, 'iJ5fWQjoKBlPvsKfXTE5KLF8yp2nHs5nuQw18Ti7.png', 1, 'App\\\Models\\\TopUp', '2022-05-12 03:55:45', '2022-05-12 03:55:45');");

            DB::statement("INSERT INTO `point_logs` (`id`, `user_id`, `point_id`, `amount`, `type`, `note`, `point_loggable_id`, `point_loggable_type`, `created_at`, `updated_at`) VALUES
            (1, 2, 1, 10000, 2, 'points given on account created for testing', NULL, NULL, '2022-05-12 05:54:47', '2022-05-12 05:54:47'),
            (2, 3, 1, 10000, 2, 'points given on account created for testing', NULL, NULL, '2022-05-12 05:55:02', '2022-05-12 05:55:02'),
            (3, 3, 2, 20000, 2, 'top up approved', 1, 'App\\\Models\\\TopUp', '2022-05-12 05:56:10', '2022-05-12 05:56:10');");

            DB::statement("INSERT INTO `point_user` (`id`, `user_id`, `point_id`, `balance`, `referrable_balance`, `created_at`, `updated_at`) VALUES
            (1, 2, 1, 10000, 10000, '2022-05-12 05:54:47', '2022-05-12 05:54:47'),
            (2, 3, 1, 10000, 10000, '2022-05-12 05:55:02', '2022-05-12 05:55:02'),
            (3, 3, 2, 20000, 20000, '2022-05-12 05:56:10', '2022-05-12 05:56:10');");
        });


        return 0;
    }
}
