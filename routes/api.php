<?php

use App\Http\Controllers\Api\PromoClaimController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/promo/claim', [PromoClaimController::class, 'claim']);
});
