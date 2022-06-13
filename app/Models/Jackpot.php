<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Jackpot extends AppModel
{
    use HasFactory;

    public function twoDigit()
    {
        return $this->belongsTo(TwoDigit::class);
    }

    public static function effectiveQuery()
    {
        return Jackpot::where('status', 1);
    }

    public static function getJackpot($fromCache = true)
    {
        if ($fromCache)
            $jackpot = Cache::rememberForever('twoDigitJackpot', function () {
                return static::effectiveQuery()
                    ->pluck('amount')->sum();
            });
        else {
            $jackpot = Cache::forget('twoDigitJackpot');
            return static::effectiveQuery()
                ->pluck('amount')->sum();
        }
        return floor($jackpot);
    }
}
