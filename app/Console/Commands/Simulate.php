<?php

namespace App\Console\Commands;

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
        for ($i = 0; $i < 365; $i++) {
            $day = today()->startOfYear()->addDay($i);
            Log::channel('debug')->info($day->format("Y-m-dD") . " " . json_encode(TwoDigitHit::checkDay($day)));
        }

        echo 'done';
    }
}
