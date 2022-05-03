<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Payment::create([
            'name' => 'KBZPay', 'mm_name' => 'ကေပေး', 'type' => 1, 'number' => '09123123123', 'account_name' => 'moon'
        ]);
        \App\Models\Payment::create([
            'name' => 'WAVEPAY (Wave Money)', 'mm_name' => 'ဝေ့ပေး ဝေ့မန်းနီး', 'type' => 2, 'number' => '09505050', 'account_name' => 'moon'
        ]);
        \App\Models\Point::create(['name' => 'Lucky Hi']);
        \App\Models\Point::create(['name' => 'MMK']);
        \App\Models\Role::create(['name' => 'admin']);
        Artisan::call('make:admin moon moon');
    }
}
