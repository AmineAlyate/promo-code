<?php

use App\Http\Controllers\Auth\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Auth\User\LoginController as UserLoginController;
use App\Http\Controllers\PromoCode\StorePromoCodeController;
use App\Http\Controllers\PromoCode\ValidatePromoCodeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/admins/login', AdminLoginController::class);
Route::post('/users/login', UserLoginController::class);

Route::middleware(['api'])->prefix('promo-codes')->group(function () {
    Route::middleware(['set-current-admin'])->group(function () {
        Route::post('/', StorePromoCodeController::class);
    });

    Route::middleware(['set-current-user'])->group(function () {
        Route::get('/validate/{code}/{price}', ValidatePromoCodeController::class);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
