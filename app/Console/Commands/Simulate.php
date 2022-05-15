<?php

namespace App\Console\Commands;

use App\Models\TwoDigit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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
        Artisan::call('cache:clear');
        return 0;
    }
}
