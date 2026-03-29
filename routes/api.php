<?php


use App\Http\Controllers\API\ForgotPasswordController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\API\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/password/forgot', [ForgotPasswordController::class, 'sendResetLink']);
Route::post('/password/reset', [ForgotPasswordController::class, 'resetPassword']);

Route::post('/logout', [AuthController::class, 'logout']);

// Route::middleware('auth.api')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout']);
// });
