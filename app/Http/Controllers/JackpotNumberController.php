<?php

namespace App\Http\Controllers;

use App\Models\JackpotNumber;
use Illuminate\Http\Request;

class JackpotNumberController extends Controller
{
    public function current()
    {
        return response()->json(JackpotNumber::orderBy('id', 'desc')->first());
    }
}
