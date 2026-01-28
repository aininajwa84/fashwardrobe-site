<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TelegramRecommendationController;
use App\Http\Controllers\Api\UserController;

Route::middleware('auth:sanctum')->group(function () {
    // Telegram integration routes
    Route::prefix('telegram')->group(function () {
        Route::post('/save-recommendations', [TelegramRecommendationController::class, 'store']);
        Route::get('/check-session/{sessionId}', [TelegramRecommendationController::class, 'checkSession']);
        Route::get('/user-recommendations', [TelegramRecommendationController::class, 'userRecommendations']);
        Route::post('/save-from-telegram', [TelegramRecommendationController::class, 'saveFromTelegram']);
    });
    
    // User verification for Telegram
    Route::get('/user/telegram-token', [UserController::class, 'generateTelegramToken']);
    Route::post('/user/verify-telegram', [UserController::class, 'verifyTelegramUser']);
});