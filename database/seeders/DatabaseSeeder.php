<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
    }
}
