<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class AppSetting extends AppModel
{
    use HasFactory;

    protected static function booted()
    {
        static::created(function ($appSetting) {
            Cache::forget('appSettingConfig');
        });
    }
    public static function current()
    {
        return Cache::rememberForever('appSettingConfig', function () {
            return AppSetting::orderBy('id', 'desc')->first();
        });
    }
}
