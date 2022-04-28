<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

class TelegramService
{

    public static function getUrl()
    {
        return 'https://api.telegram.org/bot' . env("TELEGRAM_BOT_TOKEN") . '/sendMessage';
    }
    public static function sendMessage($message, $chatId, $parseMode = 'HTML')
    {
        $options = [
            'chat_id' => $chatId,
            'parse_mode' => $parseMode,
            'reply_markup' => json_encode(['keyboard' => [[__("messages.help"), __("messages.forgot password")], ['English', 'မြန်မာ']]])
        ];
        if (is_array($message)) {
            foreach ($message as $msg) {
                $options['text'] = $msg;
                Http::get(static::getUrl(), $options);
            }
        } else {
            $options['text'] = $message;
            Http::get(static::getUrl(), $options);
        }
    }
}
