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

    public static function apkUrl($fromCache = true)
    {
        if ($fromCache)
            return Cache::rememberForever('apkUrl', function () {
                return static::orderBy('id', 'desc')->first()->url;
            });
        else {
            Cache::forget('apkUrl');
            return static::orderBy('id', 'desc')->first()->url;
        }
    }
}
