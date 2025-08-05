<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TravelRequestController;

Route::apiResource('travel-requests', TravelRequestController::class);
