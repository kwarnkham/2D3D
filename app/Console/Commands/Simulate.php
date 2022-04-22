<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
        for ($i = 0; $i < 86400; $i++) {
            Log::channel('debug')->info($i);
            Log::channel('debug')->info(json_encode(\App\Models\TwoDigit::getQueryBuilderOfEffectedNumbers(today()->addSeconds($i))->pluck('created_at')));
        }
        echo "done";
        return 0;
    }
}
