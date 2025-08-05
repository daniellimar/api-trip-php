<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TravelRequestController;

Route::post('register', [AuthController::class, 'register']);

Route::post('login', ['as' => 'login', 'uses' => 'AuthController@uses']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);

    Route::apiResource('travel-requests', TravelRequestController::class);
});
