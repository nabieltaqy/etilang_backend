<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Notifications\EmailController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\WhatsappController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\api\auth\GoogleAuthController;
use App\Http\Controllers\Api\Notifications\SMSController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('2fa-registration', [GoogleAuthController::class, 'show2faRegistration'])->middleware('auth:sanctum'); //show qr code for 2fa registration
    Route::post('2fa-verify', [GoogleAuthController::class, 'verify2fa'])->middleware(['auth:sanctum', 'ensure2FAEnabled']); //verify 2fa code
    Route::post('2fa-disable/{id}', [GoogleAuthController::class, 'disable2fa'])->middleware('auth:sanctum'); //disable 2fa only admin
});

// Middleware with auth token
Route::middleware(['auth:sanctum', 'ensure2FA'])->group(function () {
    Route::apiResource('vehicles', VehicleController::class);
    Route::apiResource('users', UserController::class);
});

Route::prefix('notifications')->group(function () {
    Route::post('send-email', [EmailController::class, 'send']); //send email
    Route::post('send-whatsapp', [WhatsappController::class, 'sendMessage']); //send whatsapp
    Route::post('send-sms', [SMSController::class, 'sendSMS']); //send SMS
});