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
use App\Http\Controllers\Api\PublicAccessController;
use Illuminate\Container\Attributes\Auth;

// use Illuminate\Http\Request;


Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('guest');
    Route::post('logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum', 'ensure2FA']);
    Route::get('2fa-registration', [GoogleAuthController::class, 'show2faRegistration'])->middleware('auth:sanctum'); //show qr code for 2fa registration
    Route::post('2fa-verify', [GoogleAuthController::class, 'verify2fa'])->middleware(['auth:sanctum', 'ensure2FAEnabled']); //verify 2fa code
    Route::get('2fa-disable/{id}', [GoogleAuthController::class, 'disable2fa'])->middleware(['auth:sanctum', 'ensure2FA', 'admin']); //disable 2fa only admin
});

// Middleware with auth token
Route::middleware(['auth:sanctum', 'ensure2FA'])->group(function () {

    // all  role can access
    Route::get('user-data', [AuthController::class, 'getUserData']); //get user data

    //only admin can access
    Route::middleware(['admin'])->group(function () {
        Route::apiResource('users', UserController::class);

    });
    
    //only police can access
    Route::middleware(['police'])->group(function () {
        Route::apiResource('vehicles', VehicleController::class);
        Route::apiResource('appeals', AppealController::class)->except(['create']);
        Route::apiResource('violations', ViolationController::class)->except(['store','show', 'updateNumber', 'verifyViolation', 'cancelViolation']);
        Route::prefix('notifications')->group(function () {
            Route::get('send-email/{id}', [NotificationController::class, 'sendEmail']); //send email
            Route::get('send-whatsapp/{id}', [NotificationController::class, 'sendWhatsApp']); //send whatsapp
            Route::get('send-sms/{id}', [NotificationController::class, 'sendSMS']); //send SMS
            Route::get('send-all/{id}', [NotificationController::class, 'sendAll']); //send all notifications
        });
        Route::apiResource('tickets', TicketController::class)->except(['create']);

        // untuk verifikasi pelanggaran (violation) yang sudah ada
        Route::get('create-token-verification/{id}', [ViolationController::class, 'createTokenForVerification'])->middleware(['checkViolationTicket']); //create token for verification
        Route::get('violations/{id}', [ViolationController::class, 'show'])->middleware(['check.ability:verify-violation', 'validate.violation.token']); //show violation
        Route::put('update-violation/{id}', [ViolationController::class, 'updateNumber'])->middleware(['check.ability:verify-violation', 'validate.violation.token']); //update number plate
        Route::put('verify-violation/{id}', [ViolationController::class, 'verifyViolation'])->middleware(['check.ability:verify-violation', 'validate.violation.token']); //verify violation
        Route::put('cancel-violation/{id}', [ViolationController::class, 'cancelViolation'])->middleware(['check.ability:verify-violation', 'validate.violation.token']); //cancel violation
        Route::post('violations/revoke-token/{id}', [ViolationController::class, 'revokeToken'])->middleware(['check.ability:verify-violation', 'validate.violation.token']); //revoke token
    });
});

//need middleware to secure the route from Edge Computing
Route::post('detected-violation', [ViolationController::class, 'store']); //send evidence from edge computing

//all user can access
Route::prefix('public')->group(function () {
Route::post('appeal', [PublicAccessController::class, 'appealStore']); //pengajuan banding
Route::post('attend-hearing', [PublicAccessController::class, 'attendHearing']); //violator chooses hearing schedule
Route::get('tickets/{id}/{number}', [PublicAccessController::class, 'showTicket']); //show ticket
Route::prefix('midtrans')->group(function () { // transaction
    Route::post('transaction', [MidtransController::class, 'createTransaction']);
    Route::post('callback', [MidtransController::class, 'callback']);
});
});
