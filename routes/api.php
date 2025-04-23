<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AppealController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ViolationController;
use App\Http\Controllers\api\auth\GoogleAuthController;
use App\Http\Controllers\Api\Notifications\SMSController;
use App\Http\Controllers\Api\Notifications\EmailController;
use App\Http\Controllers\Api\Notifications\SendAllController;
use App\Http\Controllers\Api\Notifications\WhatsappController;

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
            Route::post('send-email', [EmailController::class, 'send']); //send email
            Route::post('send-whatsapp', [WhatsappController::class, 'sendWhatsApp']); //send whatsapp
            Route::post('send-sms', [SMSController::class, 'sendSMS']); //send SMS
            Route::post('send-all', [SendAllController::class, 'sendAll']); //send all notifications
        });
    });
});

//need middleware to secure the route from Edge Computing
Route::post('detected-violation', [ViolationController::class, 'store']); //send evidence from edge computing

//all user can access
Route::post('appeals', [AppealController::class, 'store']);

//route cobaan
// Route::post('/notifications/send-all', function (Request $request) {

//     $send = [
//         'email' => $request->email,
//         'whatsapp' => $request->to,
//         'sms' => $request->to,
//     ];
//     return response()->json([
//         'message' => 'Notifications sent successfully',
//         'send' => $send,
//     ]);
// });
