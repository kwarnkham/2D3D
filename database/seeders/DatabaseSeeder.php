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
        \App\Models\Payment::create(['name' => 'KBZpay', 'number' => '09123123123']);
        \App\Models\Payment::create(['name' => 'Wave Pay', 'number' => '09123123123']);
        \App\Models\Point::create(['name' => 'Lucky Hi']);
        \App\Models\Point::create(['name' => 'MMK']);
        \App\Models\Role::create(['name' => 'admin']);
        Artisan::call('make:admin moon moon');
    }
}
