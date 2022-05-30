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
        $token = env("TELEGRAM_BOT_TOKEN");
        $host = env("APP_URL");
        $url = "https://api.telegram.org/bot$token/setWebhook?url=$host/api/$token";

        echo $url . "\n";

        $respobnse = Http::post($url);
        echo $respobnse->body();

        $token = env("TELEGRAM_ADMIN_BOT_TOKEN");
        $url = "https://api.telegram.org/bot$token/setWebhook?url=$host/api/$token";

        echo $url . "\n";

        $respobnse = Http::post($url);
        echo $respobnse->body();
    }
}
