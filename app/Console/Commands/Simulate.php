<?php

namespace App\Console\Commands;

use App\Models\TwoDigit;
use App\Models\TwoDigitHit;
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
        for ($i = 0; $i < 60 * 60 * 24; $i++) {
            $day = today()->addDay()->addSeconds($i);
            Log::channel('debug')->info($day->format("Y-m-d h:i:s A") . " " . json_encode(TwoDigit::checkTime($day)));
        }

        echo 'done';
    }
}
