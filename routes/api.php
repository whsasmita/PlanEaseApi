<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotulaController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/refresh-token', [AuthenticatedSessionController::class, 'refresh']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

    Route::apiResource('notula', NotulaController::class)->only(['index', 'show']);
    Route::apiResource('schedule', ScheduleController::class)->only(['index', 'show']);
    Route::apiResource('notification', NotificationController::class);

    Route::group(['middleware' => 'checkRole:ADMIN'], function () {
        Route::apiResource('notula', NotulaController::class);
        Route::apiResource('schedule', ScheduleController::class);
    });
});