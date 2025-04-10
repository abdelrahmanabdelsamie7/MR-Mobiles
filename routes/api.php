<?php

use App\Http\Controllers\{AuthUserController, AuthAdminController};
use App\Http\Controllers\API\{BrandController, MobileController, PaymentController, MobileColorController, MobileImageController, AccessoryController, WishlistController, CartController, CartItemController, ContactController, OrderController, StatisticsController};
use Illuminate\Http\Request;

// Route User
Route::prefix('user')->group(function () {
    Route::post('/register', [AuthUserController::class, 'register']);
    Route::post('/login', [AuthUserController::class, 'login']);
    Route::post('/logout', [AuthUserController::class, 'logout']);
    Route::get('/getaccount', [AuthUserController::class, 'getaccount']);
    Route::post('/password/forgot', [AuthUserController::class, 'forgotPassword']);
    Route::post('/password/reset', [AuthUserController::class, 'resetPassword']);
    Route::delete('/account', [AuthUserController::class, 'deleteAccount']);
    Route::get('/verify-email/{token}', [AuthUserController::class, 'verifyEmail'])
        ->name('verification.verify');
    Route::post('/resend-verification', [AuthUserController::class, 'resendVerification'])
        ->name('verification.send');
});
// Route Admin
Route::middleware('api')->prefix('admin')->group(function () {
    Route::post('/login', [AuthAdminController::class, 'login'])->name('admin.login');
    Route::post('/register', [AuthAdminController::class, 'register'])->name('admin.register');
    Route::post('/logout', [AuthAdminController::class, 'logout'])->name('admin.logout');
    Route::get('/getaccount', [AuthAdminController::class, 'getAccount'])->name('admin.getAccount');
});

Route::apiResource('brands', BrandController::class);
Route::match(['post', 'put', 'patch'], 'brands/{id}', [BrandController::class, 'update']);
Route::apiResource('mobiles', MobileController::class);
Route::apiResource('mobile-colors', MobileColorController::class);
Route::apiResource('mobile-images', MobileImageController::class);
Route::apiResource('accessories', AccessoryController::class);
Route::apiResource('wishlist', WishlistController::class);
Route::apiResource('cart', CartController::class);
Route::apiResource('cart-items', CartItemController::class);
Route::apiResource('orders', OrderController::class);
Route::apiResource('contact-us', ContactController::class);
Route::post('/contact-us/{id}/reply', [ContactController::class, 'reply'])->middleware('auth:admins');

Route::get('statistics', [StatisticsController::class, 'getStatistics']);
// Update Image

Route::match(['post', 'put', 'patch'], 'mobiles/{id}', [MobileController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'mobile-colors/{id}', [MobileColorController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'mobile-images/{id}', [MobileImageController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'accessories/{id}', [AccessoryController::class, 'update']);
Route::delete('cart', [CartController::class, 'deleteItems']);

// Payment Routes
Route::prefix('payment')->group(function () {
    Route::post('/create-checkout-session', [PaymentController::class, 'createCheckoutSession'])
        ->middleware('auth:api');
    Route::get('/success', [PaymentController::class, 'success'])
        ->name('payment.success')
        ->withoutMiddleware(['auth:api']);
    Route::get('/cancel', [PaymentController::class, 'cancel'])
        ->name('payment.cancel')
        ->withoutMiddleware(['auth:api']);
});

// Debug route
Route::get('/api/debug/payment', function (Request $request) {
    \Log::channel('paymob')->info('Debug Payment Callback:', [
        'method' => $request->method(),
        'data' => $request->all(),
        'query' => $request->query(),
        'headers' => $request->headers->all()
    ]);
    return response()->json(['status' => 'debug', 'data' => $request->all()]);
})->withoutMiddleware(['auth:api', 'throttle:api']);