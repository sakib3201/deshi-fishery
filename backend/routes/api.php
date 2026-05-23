<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\CurrentFarmController;
use App\Http\Controllers\Api\V1\FarmController;
use App\Http\Controllers\Api\V1\FarmMemberController;
use App\Http\Controllers\Api\V1\PondController;
use App\Http\Controllers\Api\V1\SaleController;
use App\Http\Controllers\Api\V1\StockReleaseController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/auth')->middleware('throttle:60,1')->group(function (): void {
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
    Route::get('ponds', [PondController::class, 'index']);
    Route::get('ponds/{pond}', [PondController::class, 'show']);
    Route::post('ponds', [PondController::class, 'store'])->middleware('farm.role:owner,manager');
    Route::patch('ponds/{pond}', [PondController::class, 'update'])->middleware('farm.role:owner,manager');
    Route::delete('ponds/{pond}', [PondController::class, 'destroy'])->middleware('farm.role:owner,manager');
    Route::get('farms/{farm}/members', [FarmMemberController::class, 'index']);
    Route::post('farms/{farm}/members', [FarmMemberController::class, 'store']);
    Route::delete('farms/{farm}/members/{user}', [FarmMemberController::class, 'destroy']);
    Route::patch('users/current-farm', [CurrentFarmController::class, 'update']);

    Route::get('stock-releases', [StockReleaseController::class, 'index']);
    Route::get('stock-releases/{stockRelease}', [StockReleaseController::class, 'show']);
    Route::post('stock-releases', [StockReleaseController::class, 'store'])->middleware('farm.role:owner,manager');
    Route::patch('stock-releases/{stockRelease}', [StockReleaseController::class, 'update'])->middleware('farm.role:owner,manager');
    Route::delete('stock-releases/{stockRelease}', [StockReleaseController::class, 'destroy'])->middleware('farm.role:owner,manager');

    Route::get('sales', [SaleController::class, 'index']);
    Route::get('sales/{sale}', [SaleController::class, 'show']);
    Route::post('sales', [SaleController::class, 'store'])->middleware('farm.role:owner,manager,worker');
    Route::patch('sales/{sale}', [SaleController::class, 'update'])->middleware('farm.role:owner,manager');
    Route::delete('sales/{sale}', [SaleController::class, 'destroy'])->middleware('farm.role:owner,manager');
});
