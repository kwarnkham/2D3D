<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class JackPot extends AppModel
{
    use HasFactory;

    public function twoDigit()
    {
        return $this->belongsTo(TwoDigit::class);
    }

    public static function effectiveQuery()
    {
        return JackPot::where('status', 1);
    }

    public static function getJackPot($fromCache = true)
    {
        if ($fromCache)
            return Cache::rememberForever('twoDigitJackPot', function () {
                return static::effectiveQuery()
                    ->pluck('amount')->sum();
            });
        else {
            Cache::forget('twoDigitJackPot');
            return static::effectiveQuery()
                ->pluck('amount')->sum();
        }
    }
}
