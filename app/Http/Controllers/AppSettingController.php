<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    public static function current()
    {
        return response()->json(AppSetting::current());
    }
}
