<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/discountgiftbot/webhook', [App\Http\Controllers\DiscountGiftBot\BotController::class, 'set'])->name('webhook-set');
Route::post('/discountgiftbot/webhook', [App\Http\Controllers\DiscountGiftBot\BotController::class, 'handle'])->name('webhook-handle');

Route::group(['prefix' => 'J7lx6VYUiZ4DVXbOOKbB61ZGifBQyt'], function() {
    Route::post('callback', [App\Http\Controllers\Api\NowPaymentsController::class, 'callback'])->name('nowpayments.callback');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
