<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{

    public static function getUrl()
    {
        return 'https://api.telegram.org/bot' . env("TELEGRAM_BOT_TOKEN") . '/sendMessage';
    }
    public static function sendMessage(string $message, $chatId, $parseMode = 'HTML')
    {
        $response = Http::get(static::getUrl(), [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => $parseMode,
            'reply_markup' => json_encode(['keyboard' => [['Top up', 'Help', 'Forgot Password'], ['Promotion']]])
        ]);

        Log::info($response->json());
    }
}
