<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\TopUpController;
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

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/change-password', 'changePassword')->middleware('auth:sanctum');
});

Route::middleware(['auth:sanctum'])->controller(TopUpController::class)->group(function () {
    Route::post('/top-up', 'store');
});

Route::middleware(['auth:sanctum'])->controller(PaymentController::class)->group(function () {
    Route::get('/payment', 'index');
});
