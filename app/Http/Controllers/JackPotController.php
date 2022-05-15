<?php

namespace App\Http\Controllers;

use App\Models\JackPot;
use App\Models\TwoDigit;
use Illuminate\Http\Request;


class JackPotController extends Controller
{
    public function twoDigitJackPot()
    {
        return response()->json(JackPot::getJackPot());
    }
}
