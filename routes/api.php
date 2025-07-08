<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotulaController;
use App\Http\Controllers\PollingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/refresh-token', [AuthenticatedSessionController::class, 'refresh']);

Route::get('/profile-photo/{profile}', [ProfileController::class, 'getPhoto'])
    ->name('profile.photo');

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

    Route::apiResource('profile', ProfileController::class);

    Route::apiResource('polling', PollingController::class)
        ->only(['index', 'show']);
    Route::post('polling/{polling}/vote', [PollingController::class, 'vote']);
    Route::get('polling/{polling}/results', [PollingController::class, 'results']);

    Route::apiResource('notula', NotulaController::class)
        ->only(['index', 'show']);

    Route::apiResource('schedule', ScheduleController::class)
        ->only(['index', 'show']);

    // Route::apiResource('notification', NotificationController::class);

    Route::post('/send-notification-to-user', [NotificationController::class, 'sendTestNotificationToUser']);
    Route::post('/send-topic-notification', [NotificationController::class, 'sendTopicNotification']);

    // Rute untuk mengelola FCM Token
    Route::post('/fcm-token', [FcmTokenController::class, 'store']);
    Route::delete('/fcm-token', [FcmTokenController::class, 'destroy']);

    Route::group(['middleware' => 'checkRole:ADMIN'], function () {
        Route::apiResource('polling', PollingController::class)
            ->only(['store', 'update', 'destroy']);
        Route::delete('/polling/{polling}/image', [PollingController::class, 'deleteImage']);

        Route::apiResource('notula', NotulaController::class)
            ->only(['store', 'update', 'destroy']);

        Route::apiResource('schedule', ScheduleController::class)
            ->only(['store', 'update', 'destroy']);
    });
});
