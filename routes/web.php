<?php

use App\Models\AppSetting;
use App\Models\AppVersion;
use App\Models\Jackpot;
use App\Models\JackpotNumber;
use App\Models\Point;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return response()->json([
    //     'user' => User::find(2)->load(User::RS),
    //     'app_verison' => AppVersion::current(),
    //     'app_setting' => AppSetting::current(),
    //     'jackpot_number' => JackpotNumber::current(),
    //     'jackpot' => Jackpot::getJackpot(),
    //     'points' => Cache::rememberForever('points', function () {
    //         return Point::all();
    //     })
    // ]);
    return view('welcome');
});
