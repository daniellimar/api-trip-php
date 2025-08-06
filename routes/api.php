<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{AuthController, NotificationController, TravelRequestController};

Route::post('register', [AuthController::class, 'register']);

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);

    Route::apiResource('travel-requests', TravelRequestController::class);

    Route::get('/notificacoes', function () {
        return auth()->user()->notifications;
    });
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
});
