<?php

namespace App\Http\Controllers;

use App\Models\JackpotNumber;
use Illuminate\Http\Request;

class JackpotNumberController extends Controller
{
    public static function current()
    {
        return response()->json(JackpotNumber::current());
    }
}
