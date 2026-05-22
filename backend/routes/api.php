<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\CurrentFarmController;
use App\Http\Controllers\Api\V1\FarmController;
use App\Http\Controllers\Api\V1\FarmMemberController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/auth')->middleware('throttle:5,15')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::prefix('v1')->middleware('auth:api')->group(function (): void {
    Route::apiResource('farms', FarmController::class);
    Route::get('farms/{farm}/members', [FarmMemberController::class, 'index']);
    Route::post('farms/{farm}/members', [FarmMemberController::class, 'store']);
    Route::delete('farms/{farm}/members/{user}', [FarmMemberController::class, 'destroy']);
    Route::patch('users/current-farm', [CurrentFarmController::class, 'update']);
});
