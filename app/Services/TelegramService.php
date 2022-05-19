<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TelegramService
{

    public static function getUrl()
    {
        return 'https://api.telegram.org/bot' . env("TELEGRAM_BOT_TOKEN");
    }
    public static function sendMessage($message, $chatId, $parseMode = 'HTML')
    {
        $options = [
            'chat_id' => $chatId,
            'parse_mode' => $parseMode,
            'reply_markup' => json_encode(['keyboard' => [[__("messages.help"), __("messages.application"), __("messages.forgot password")], ['English', 'မြန်မာ']]])
        ];
        if (is_array($message)) {
            foreach ($message as $msg) {
                $options['text'] = $msg;
                Http::get(static::getUrl() . '/sendMessage', $options);
            }
        } else {
            $options['text'] = $message;
            Http::get(static::getUrl() . '/sendMessage', $options);
        }
    }

    public static function getLink($fresh = false)
    {
        if (!$fresh)
            $botInfo = json_decode(Cache::rememberForever('telegramBotInfo', function () {
                return Http::get(static::getUrl() . '/getMe')->body();
            }), true);
        else {
            Cache::forget('telegramBotInfo');
            $botInfo = Http::get(static::getUrl() . '/getMe')->json();
        }

        return "https://t.me/" . $botInfo['result']['username'];
    }
}
