<?php

namespace App\Http\Controllers;

use App\Models\JackPot;
use App\Models\TwoDigit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class JackPotController extends Controller
{
    public function twoDigitJackPot()
    {
        $jackpot = Cache::rememberForever('twoDigitJackPot', function () {
            return JackPot::where('jack_potable_type', TwoDigit::class)->pluck('amount')->sum();
        });
        return response()->json($jackpot);
    }
}
