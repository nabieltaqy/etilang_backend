<?php

use App\Models\Notification;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AppealController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ViolationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Auth\GoogleAuthController;
use App\Http\Controllers\Api\MidtransController;

// use Illuminate\Http\Request;


Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('guest');
    Route::get('logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum', 'ensure2FA']);
    Route::get('2fa-registration', [GoogleAuthController::class, 'show2faRegistration'])->middleware('auth:sanctum'); //show qr code for 2fa registration
    Route::post('2fa-verify', [GoogleAuthController::class, 'verify2fa'])->middleware(['auth:sanctum', 'ensure2FAEnabled']); //verify 2fa code
    Route::post('2fa-disable/{id}', [GoogleAuthController::class, 'disable2fa'])->middleware(['auth:sanctum', 'ensure2FA']); //disable 2fa only admin
});

// Middleware with auth token
Route::middleware(['auth:sanctum', 'ensure2FA'])->group(function () {

    //only admin can access
    Route::middleware(['admin'])->group(function () {
        Route::apiResource('users', UserController::class);
    });
    
    //only police can access
    Route::middleware(['police'])->group(function () {
        Route::apiResource('vehicles', VehicleController::class);
        Route::apiResource('appeals', AppealController::class)->except(['create']);
        Route::apiResource('violations', ViolationController::class)->except(['store']);
        Route::prefix('notifications')->group(function () {
            Route::post('send-email', [NotificationController::class, 'sendEmail']); //send email
            Route::post('send-whatsapp', [NotificationController::class, 'sendWhatsApp']); //send whatsapp
            Route::post('send-sms', [NotificationController::class, 'sendSMS']); //send SMS
            Route::post('send-all', [NotificationController::class, 'sendAll']); //send all notifications
        });
        Route::apiResource('tickets', TicketController::class)->except(['create']);
    });
});

//need middleware to secure the route from Edge Computing
Route::post('detected-violation', [ViolationController::class, 'store']); //send evidence from edge computing

//all user can access
Route::post('appeals', [AppealController::class, 'store']);
Route::prefix('midtrans')->group(function () {
    Route::post('transaction', [MidtransController::class, 'createTransaction']);
    // Route::get('callback', [MidtransController::class, 'getTransactionStatus']);
});