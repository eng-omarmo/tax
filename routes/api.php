<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\OtpController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\landlordController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\GlobalCheckController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// API v1 Routes
Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::prefix('otp')->controller(OtpController::class)->group(function () {
        Route::post('/send', 'send');
        Route::post('/verify', 'verify');
    });
    // Global check endpoints (no auth required)
    Route::prefix('check')->controller(GlobalCheckController::class)->group(function () {
        Route::post('/email', 'checkEmail');
        Route::post('/phone', 'checkPhone');
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/branches', [GlobalCheckController::class, 'branch']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::prefix('landlord')->controller(LandlordController::class)->group(function () {
            Route::get('/{id}', 'show');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
        Route::prefix('property')->controller(PropertyController::class)->group(function () {
            Route::get('/{id}', 'show');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
        Route::prefix('unit')->controller(UnitController::class)->group(function () {
            Route::get('/{id}', 'show');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
    });
});
