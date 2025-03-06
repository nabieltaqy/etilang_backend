<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Routing\RouteGroup;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

// Middleware with auth token
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('user', [UserController::class, 'index']);
});