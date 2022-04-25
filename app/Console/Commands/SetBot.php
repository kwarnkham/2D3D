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
        $respobnse = Http::post("https://api.telegram.org/bot$token/setWebhook?url=$host/api/$token");
        echo $respobnse->body();
    }
}
