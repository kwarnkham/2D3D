<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class InitDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize the data for database';

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
        \App\Models\AppSetting::create([
            'pool_amount' => '1000000',
            'jackpot_rate' => '0.1',
            'referral_rate' => '0.05',
            'rate' => '85'
        ]);
        \App\Models\Payment::create([
            'name' => 'KBZPay', 'mm_name' => 'ကေပေး', 'type' => 1, 'number' => null, 'account_name' => 'SAI KWARN KHAM', 'qr' => 'https://lunarblessing.sgp1.cdn.digitaloceanspaces.com/QR/KpayQR.PNG'
        ]);
        \App\Models\Payment::create([
            'name' => 'WAVEPAY (Wave Money)', 'mm_name' => 'ဝေ့ပေး ဝေ့မန်းနီး', 'type' => 2, 'number' => '09792761207',
        ]);
        \App\Models\Payment::create([
            'name' => 'WAVEPAY (Wave Money) 2', 'mm_name' => 'ဝေ့ပေး ဝေ့မန်းနီး', 'type' => 2, 'number' => '09452538242',
        ]);
        \App\Models\Point::create(['name' => 'Lucky Hi']);
        \App\Models\Point::create(['name' => 'MMK']);
        \App\Models\Role::create(['name' => 'admin']);
        \App\Models\AppVersion::create(['version' => '1.0.0']);
        \App\Models\JackpotNumber::create(['number' => 0]);
        Artisan::call('make:admin moon ninjamoon');

        if (env("APP_ENV") == 'local')
            DB::transaction(function () {
                DB::statement("INSERT INTO `account_providers` (`id`, `name`, `created_at`, `updated_at`) VALUES
        (1, 'telegram', '2022-05-12 03:51:05', '2022-05-12 03:51:05');");

                DB::statement("INSERT INTO `users` (`id`, `referrer_id`, `name`, `locale`, `banned_at`, `password`, `created_at`, `updated_at`) VALUES
        (2, 1, 'moon2', 'my', NULL, '$2y$10$2BKTX5E4P1/Be/Q3Q.TME.tP5rHx.bFPWZj0fSUHiFmE3qEQsPD6q', '2022-05-13 02:04:27', '2022-05-13 02:04:27'),
        (3, 2, 'moon1', 'my', NULL, '$2y$10$2BKTX5E4P1/Be/Q3Q.TME.tP5rHx.bFPWZj0fSUHiFmE3qEQsPD6q', '2022-05-13 02:04:40', '2022-05-13 02:05:11');");

                DB::statement("INSERT INTO `account_provider_user` (`id`, `user_id`, `account_provider_id`, `provider_id`, `username`, `sent_at`, `created_at`, `updated_at`) VALUES
        (1, 2, 1, 5144374717, NULL, '2022-05-13 02:03:05', '2022-05-13 02:04:27', '2022-05-13 02:04:27'),
        (2, 3, 1, 1391365941, 'lunablessing', '2022-05-13 02:04:40', '2022-05-13 02:04:40', '2022-05-13 02:04:40');");

                DB::statement("INSERT INTO `top_ups` (`id`, `user_id`, `payment_id`, `status`, `amount`, `denied_reason`, `created_at`, `updated_at`) VALUES
        (1, 3, 1, 2, 20000, NULL, '2022-05-13 02:06:30', '2022-05-13 02:07:06');");

                DB::statement("INSERT INTO `approved_top_ups` (`id`, `top_up_id`, `created_at`, `updated_at`) VALUES
        (1, 1, '2022-05-13 02:07:06', '2022-05-13 02:07:06');");

                DB::statement("INSERT INTO `pictures` (`id`, `name`, `pictureable_id`, `pictureable_type`, `created_at`, `updated_at`) VALUES
        (1, '9DhKB70O7s8kjUuZx7q8RhFjyCMeRKBM1NFSZCtd.png', 1, 'App\\\Models\\\TopUp', '2022-05-13 02:06:31', '2022-05-13 02:06:31'),
        (2, 'oiEpdpW1s8CuohqJXP664gi9ZKakPWt86VJ6zOxZ.png', 1, 'App\\\Models\\\ApprovedTopUp', '2022-05-13 02:07:06', '2022-05-13 02:07:06');");

                DB::statement("INSERT INTO `point_logs` (`id`, `user_id`, `point_id`, `amount`, `type`, `note`, `point_loggable_id`, `point_loggable_type`, `created_at`, `updated_at`) VALUES
        (1, 2, 1, 10000, 2, 'points given on account created for testing', NULL, NULL, '2022-05-13 02:04:27', '2022-05-13 02:04:27'),
        (2, 3, 1, 10000, 2, 'points given on account created for testing', NULL, NULL, '2022-05-13 02:04:40', '2022-05-13 02:04:40'),
        (3, 3, 2, 20000, 2, 'top up approved', 1, 'App\\\Models\\\TopUp', '2022-05-13 02:07:06', '2022-05-13 02:07:06');");

                DB::statement("INSERT INTO `point_user` (`id`, `user_id`, `point_id`, `balance`, `referrable_balance`, `created_at`, `updated_at`) VALUES
        (1, 2, 1, 10000, 10000, '2022-05-13 02:04:27', '2022-05-13 02:04:27'),
        (2, 3, 1, 10000, 10000, '2022-05-13 02:04:40', '2022-05-13 02:04:40'),
        (3, 3, 2, 20000, 20000, '2022-05-13 02:07:06', '2022-05-13 02:07:06');");
            });
    }
}
