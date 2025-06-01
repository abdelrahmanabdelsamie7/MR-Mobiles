<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\Api\OrderController;
Route::prefix('user')->group(function () {
    Route::post('/register', [AuthUserController::class, 'register']);
    Route::post('/login', [AuthUserController::class, 'login']);
    Route::post('/logout', [AuthUserController::class, 'logout']);
    Route::get('/getaccount', [AuthUserController::class, 'getaccount']);
    Route::post('/password/forgot', [AuthUserController::class, 'forgotPassword']);
    Route::post('/password/reset', [AuthUserController::class, 'resetPassword']);
    Route::delete('/account', [AuthUserController::class, 'deleteAccount']);
    Route::get('/verify-email/{token}', [AuthUserController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/resend-verification', [AuthUserController::class, 'resendVerification'])->name('verification.send');
});
Route::middleware('auth:api')->group(function () {
    Route::get('/user/orders', [OrderController::class, 'userOrders']);
});