<?php

namespace App\Http\Controllers;

use App\Models\Jackpot;
use App\Models\TwoDigit;
use Illuminate\Http\Request;


class JackpotController extends Controller
{
    public function twoDigitJackpot()
    {
        return response()->json(Jackpot::getJackpot());
    }
}
