<?php

namespace App\Models;

use App\Services\TelegramService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class AppVersion extends AppModel
{
    use HasFactory;

    protected static function booted()
    {
        static::created(function ($appVersion) {
            Cache::forget('appVersion');
        });
    }

    protected $appends = ['telegram_bot', 'apk_url'];
    public function telegramBot(): Attribute
    {
        return new Attribute(
            get: fn () => TelegramService::getLink()
        );
    }

    public function apkUrl(): Attribute
    {
        $app = static::orderBy('id', 'desc')->first();
        $array = explode('.', $app->url);
        $array[2] .= $app->version;
        return new Attribute(
            get: fn () => implode(".", $array),
        );
    }

    public static function current()
    {
        return Cache::rememberForever('appVersion', function () {
            return AppVersion::orderBy('id', 'desc')->first();
        });
    }
}
