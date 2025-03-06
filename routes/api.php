<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\api\auth\GoogleAuthController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('2fa-registration', [GoogleAuthController::class, 'show2faRegistration'])->middleware('auth:sanctum'); //show qr code for 2fa registration
    Route::post('2fa-verify', [GoogleAuthController::class, 'verify2fa'])->middleware('auth:sanctum'); //verify 2fa code
    Route::post('2fa-disable/{id}', [GoogleAuthController::class, 'disable2fa'])->middleware('auth:sanctum'); //disable 2fa only admin but not applied yet
});

// Middleware with auth token
Route::middleware(['auth:sanctum', 'ensure2FA'])->group(function () {
    Route::apiResource('users', UserController::class);
});