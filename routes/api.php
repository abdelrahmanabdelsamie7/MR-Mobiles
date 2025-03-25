<?php

use App\Http\Controllers\{AuthUserController,AuthAdminController};
use App\Http\Controllers\API\{BrandController,MobileController,MobileColorController,MobileImageController,AccessoryController,WishlistController,CartController,CartItemController,ContactController,PaymentController,OrderController,StatisticsController};
Route::apiResource('brands' , BrandController::class);
Route::apiResource('mobiles' , MobileController::class);
Route::apiResource('mobile-colors' , MobileColorController::class);
Route::apiResource('mobile-images' , MobileImageController::class);
Route::apiResource('accessories' , AccessoryController::class);
Route::apiResource('wishlist' , WishlistController::class);
Route::apiResource('cart' , CartController::class);
Route::apiResource('cart-items' , CartItemController::class);
Route::apiResource('orders' , OrderController::class);
Route::apiResource('contact-us' , ContactController::class);
Route::get('statistics', [StatisticsController::class , 'getStatistics']);
// Update Image
Route::match(['post', 'put', 'patch'], 'brands/{id}', [BrandController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'mobiles/{id}', [MobileController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'mobile-colors/{id}', [MobileColorController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'mobile-images/{id}', [MobileImageController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'accessories/{id}', [AccessoryController::class, 'update']);
Route::delete('cart' , [CartController::class , 'deleteItems']);
// Route User
 Route::prefix('user')->group(function () {
        // Public routes (no auth required)
        Route::post('/login', [AuthUserController::class, 'login'])->name('user.login');
        Route::post('/register', [AuthUserController::class, 'register'])->name('user.register');
        Route::post('/password/forgot', [AuthUserController::class, 'forgotPassword'])->name('user.password.forgot');
        Route::post('/password/reset', [AuthUserController::class, 'resetPassword'])->name('user.password.reset');
        Route::get('/verify-email/{token}', [AuthUserController::class, 'verifyEmail'])->name('user.verify.email');
        Route::post('/resend-email', [AuthUserController::class, 'resendVerification'])->name('user.resend.verification');
        Route::middleware('auth:api')->group(function () {
            Route::get('/getaccount', [AuthUserController::class, 'getAccount'])->name('user.getAccount');
            Route::post('/logout', [AuthUserController::class, 'logout'])->name('user.logout');
            Route::delete('/account', [AuthUserController::class, 'deleteAccount'])->name('user.account.delete');    });
});
// Route Admin
Route::middleware('api')->prefix('admin')->group(function()  {
        Route::post('/login', [AuthAdminController::class, 'login'])->name('admin.login');
        Route::post('/register', [AuthAdminController::class, 'register'])->name('admin.register');
        Route::post('/logout', [AuthAdminController::class, 'logout'])->name('admin.logout');
        Route::get('/getaccount', [AuthAdminController::class, 'getAccount'])->name('admin.getAccount');
});
Route::post('/payment/create-checkout-session', [PaymentController::class, 'createCheckoutSession']);
Route::get('/payment/success', [PaymentController::class, 'success']);
Route::get('/payment/cancel', [PaymentController::class, 'cancel']);