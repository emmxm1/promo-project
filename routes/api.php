<?php

use App\Http\Controllers\Api\PromoClaimController;
use App\Http\Controllers\Api\PromoHistoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/promo/claim', [PromoClaimController::class, 'claim']);
    Route::get('/promo/history', [PromoHistoryController::class, 'index']);
});
