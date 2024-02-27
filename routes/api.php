<?php

use App\Http\Controllers\OTPController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('password')->controller(OTPController::class)->group(function () {

    Route::get('/otp', 'generateOTP');
    Route::get('/otp/{code}', 'validateOTP');

    Route::get('/hotp', 'generateHOTP');
    Route::get('/hotp/{code}', 'validateHOTP');

    Route::get('/totp', 'generateTOTP');
    Route::get('/totp/{code}', 'validateTOTP');

});
