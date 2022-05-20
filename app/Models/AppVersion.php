<?php

namespace App\Models;

use App\Services\TelegramService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class AppVersion extends AppModel
{
    use HasFactory;


    protected $appends = ['telegram_bot'];
    public function telegramBot(): Attribute
    {
        return new Attribute(
            get: fn () => TelegramService::getLink()
        );
    }

    public static function apkUrl()
    {
        $app = static::orderBy('id', 'desc')->first();
        $array = explode('.', $app->url);
        $array[3] .= $app->version;
        return implode(".", $array);
    }
}
