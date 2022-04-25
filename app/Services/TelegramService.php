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
    public static function sendMessage($message, $chatId, $parseMode = 'HTML')
    {
        if (is_array($message)) {
            foreach ($message as $msg) {
                Http::get(static::getUrl(), [
                    'chat_id' => $chatId,
                    'text' => $msg,
                    'parse_mode' => $parseMode,
                    'reply_markup' => json_encode(['keyboard' => [['Top up', 'Help', 'Forgot Password'], ['Promotion']]])
                ]);
            }
        } else
            Http::get(static::getUrl(), [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => $parseMode,
                'reply_markup' => json_encode(['keyboard' => [['Top up', 'Help', 'Forgot Password'], ['Promotion']]])
            ]);
    }
}
