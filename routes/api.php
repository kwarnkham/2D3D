<?php

use App\Http\Controllers\AppSettingController;
use App\Http\Controllers\AppVersionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JackpotController;
use App\Http\Controllers\JackpotNumberController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\PointLogController;
use App\Http\Controllers\ReferralRewardController;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\TopUpController;
use App\Http\Controllers\TwoDigitController;
use App\Http\Controllers\TwoDigitHitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/' . env('TELEGRAM_BOT_TOKEN'), [TelegramWebhookController::class, 'handle']);
Route::post('/' . env('TELEGRAM_ADMIN_BOT_TOKEN'), [TelegramWebhookController::class, 'handleAdmin']);

Route::get('/app-version', [AppVersionController::class, 'index']);

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->middleware(['disallowBanned']);
    Route::post('/change-password', 'changePassword')->middleware('auth:sanctum');
    Route::post('/reset-password', 'resetPassword')->name('resetPassword')->middleware('signed');
});

Route::middleware(['auth:sanctum'])->controller(TopUpController::class)->group(function () {
    Route::post('/top-up', 'store');
    Route::get('/top-up/{topUp}', 'find');
    Route::get('/top-up', 'index');
    Route::post('/top-up/approve/{topUp}', 'approve');
    Route::post('/top-up/draft/{topUp}', 'draft');
    Route::post('/top-up/deny/{topUp}', 'deny');
    Route::post('/top-up/cancel/{topUp}', 'cancel');
});

Route::middleware(['auth:sanctum'])->controller(PaymentController::class)->group(function () {
    Route::get('/payment', 'index');
});

Route::middleware(['auth:sanctum'])->controller(ReferralRewardController::class)->group(function () {
    Route::get('/referral-reward/{referralReward}', 'find');
});

Route::middleware(['auth:sanctum'])->controller(UserController::class)->group(function () {
    Route::get('/me', 'me');
    Route::get('/user', 'index');
    Route::post('/user/ban/{user}', 'ban');
    Route::post('/user/un-ban/{user}', 'unBan');
    Route::post('/user/set-locale', 'setLocale');
    Route::post('/user/{user}', 'update');
    Route::get('/referees', 'getReferees');
});

Route::middleware(['auth:sanctum'])->controller(TwoDigitController::class)->group(function () {
    Route::post('/two-digit', 'store')->middleware(['disallowBanned']);
    Route::get('/two-digit', 'index');
    Route::get('/two-digit/{twoDigit}', 'find');
});

Route::middleware(['auth:sanctum'])->controller(TwoDigitHitController::class)->group(function () {
    Route::post('/two-digit-hit', 'store');
    Route::get('/two-digit-hit/{twoDigitHit}/point-log/{pointLog}', 'find');
    Route::get('/two-digit-hit', 'index');
    Route::get('/two-digit-hit/latest', 'latest');
});

Route::middleware(['auth:sanctum'])->controller(WithdrawController::class)->group(function () {
    Route::post('/withdraw', 'store')->middleware(['disallowBanned']);
    Route::get('/withdraw', 'index');
    Route::post('/withdraw/approve/{withdraw}', 'approve');
    Route::post('/withdraw/draft/{withdraw}', 'draft');
    Route::post('/withdraw/deny/{withdraw}', 'deny');
    // Route::post('/withdraw/cancel/{withdraw}', 'cancel');
    Route::get('/withdraw/{withdraw}', 'find');
});

Route::middleware(['auth:sanctum'])->controller(PointLogController::class)->group(function () {
    Route::get('/point-log', 'index');
});

Route::middleware(['auth:sanctum'])->controller(PointController::class)->group(function () {
    Route::get('/point', 'index');
});

Route::controller(JackpotController::class)->group(function () {
    Route::get('/jackpot', 'twoDigitJackpot');
});

Route::controller(JackpotNumberController::class)->group(function () {
    Route::get('/jackpot-number', 'current');
});

Route::controller(AppSettingController::class)->group(function () {
    Route::get('/app-setting', 'current');
    Route::post('/app-setting', 'store');
});
