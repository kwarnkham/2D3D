<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SetBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:bot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the bot';

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
        $token = config('app')['telegram_bot_token'];
        $host = config('app')['url'];
        $url = "https://api.telegram.org/bot$token/setWebhook?url=$host/api/$token";

        echo $url . "\n";

        $respobnse = Http::post($url);
        echo $respobnse->body();

        $token = config('app')['telegram_admin_bot_token'];
        $url = "https://api.telegram.org/bot$token/setWebhook?url=$host/api/$token";

        echo $url . "\n";

        $respobnse = Http::post($url);
        echo $respobnse->body();
    }
}
