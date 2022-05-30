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
        $resposne = Http::get('https://www.myanmar123.com/two-d');
        $str = trim(preg_replace("/\s+|\n+|\r/", ' ', $resposne->body()));

        $first = '\<tr\> \<td\>' . str_replace('/', '\/', today()->subDays(3)->format("d/m/Y")) . '\<\/td\> \<td class="text-center"\>12\:01\:00 PM\<\/td\> ';

        // $first = '\<tr\> \<td\>' . str_replace('/', '\/', today()->subDays(3)->format("d/m/Y")) . '\<\/td\> \<td class="text-center"\>04\:31\:00 PM\<\/td\> ';
        $second = '\<\/tr\>';
        $number = null;
        $set = null;
        $value = null;
        if (preg_match("/$first(.*?)$second/", $str, $match)) {
            $first = '"\>';
            $second = '\<\/td\>';
            if (preg_match_all("/$first(.*?)$second/", $match[1], $found)) {
                $found[1] = array_map(fn ($value) => str_replace(',', '', $value), $found[1]);
                if (is_numeric($found[1][0]) && is_numeric($found[1][1]) && is_numeric($found[1][2])) {
                    $number = $found[1][0];
                    $set = $found[1][1];
                    $value = $found[1][2];
                    echo $set;
                    echo PHP_EOL;
                    echo $value;
                    echo PHP_EOL;
                    echo $number;
                }
            }
        }
        echo 'done';
    }
}
