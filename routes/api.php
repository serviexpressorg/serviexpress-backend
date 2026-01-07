<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\JwtAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware([JwtAuthMiddleware::class])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me/update', [AuthController::class, 'updateProfile']);
});
