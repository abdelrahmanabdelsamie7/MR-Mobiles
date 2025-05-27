<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthAdminController;
Route::middleware('api')->prefix('admin')->group(function () {
    Route::post('/login', [AuthAdminController::class, 'login']);
    Route::middleware('auth:admins')->group(function () {
        Route::post('/add', [AuthAdminController::class, 'addAdmin']);
        Route::get('/getaccount', [AuthAdminController::class, 'getAccount']);
        Route::post('/logout', [AuthAdminController::class, 'logout']);
        Route::post('/refresh', [AuthAdminController::class, 'refresh']);
    });
});