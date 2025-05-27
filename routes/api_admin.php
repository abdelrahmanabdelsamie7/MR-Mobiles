<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthAdminController;
Route::middleware('api')->prefix('admin')->group(function () {
    Route::post('/login', [AuthAdminController::class, 'login'])->name('admin.login');
    Route::post('/register', [AuthAdminController::class, 'register'])->name('admin.register');
    Route::post('/logout', [AuthAdminController::class, 'logout'])->name('admin.logout');
    Route::get('/getaccount', [AuthAdminController::class, 'getAccount'])->name('admin.getAccount');
});