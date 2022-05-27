<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class JackpotNumber extends AppModel
{
    use HasFactory;
    protected static function booted()
    {
        static::created(function ($jackpotNumber) {
            Cache::forget('jackpotNumber');
        });
    }
    public static function current()
    {
        return Cache::rememberForever('jackpotNumber', function () {
            return JackpotNumber::orderBy('id', 'desc')->first();
        });
    }
}
