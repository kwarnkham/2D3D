<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TelegramService
{

    public static function getUrl($admin = false)
    {
        return 'https://api.telegram.org/bot' . ($admin ? config('app')['telegram_admin_bot_token'] : config('app')['telegram_bot_token']);
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

    public static function sendAdminMessage($message)
    {
        $options = [
            'chat_id' => config('app')['telegram_receiver'],
            'parse_mode' => 'HTML',
        ];
        if (is_array($message)) {
            foreach ($message as $msg) {
                $options['text'] = $msg;
                Http::get(static::getUrl(true) . '/sendMessage', $options);
            }
        } else {
            $options['text'] = $message;
            Http::get(static::getUrl(true) . '/sendMessage', $options);
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
