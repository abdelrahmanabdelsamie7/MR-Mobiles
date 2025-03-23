<?php

use App\Http\Controllers\{AuthUserController,AuthAdminController};
use App\Http\Controllers\API\{BrandController,MobileController,MobileColorController,MobileImageController,AccessoryController,WishlistController,CartController,CartItemController,ContactController};
Route::apiResource('brands' , BrandController::class);
Route::apiResource('mobiles' , MobileController::class);
Route::apiResource('mobile-colors' , MobileColorController::class);
Route::apiResource('mobile-images' , MobileImageController::class);
Route::apiResource('accessories' , AccessoryController::class);
Route::apiResource('wishlist' , WishlistController::class);
Route::apiResource('cart' , CartController::class);
Route::apiResource('cart-items' , CartItemController::class);
Route::apiResource('contact-us' , ContactController::class);


// Update Image
Route::match(['post', 'put', 'patch'], 'brands/{id}', [BrandController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'mobiles/{id}', [MobileController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'mobile-colors/{id}', [MobileColorController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'mobile-images/{id}', [MobileImageController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'accessories/{id}', [AccessoryController::class, 'update']);
Route::delete('cart' , [CartController::class , 'deleteItems']);
// Route User
Route::prefix('user')->middleware(['api'])->group(function() {
    Route::post('/login', [AuthUserController::class, 'login']);
    Route::post('/logout', [AuthUserController::class, 'logout']);
    Route::post('/register', [AuthUserController::class, 'register']);
    Route::get('/getaccount', [AuthUserController::class, 'getaccount']);
});
// Route Admin
Route::middleware('api')->prefix('admin')->group(function()  {
        Route::post('/login', [AuthAdminController::class, 'login'])->name('admin.login');
        Route::post('/register', [AuthAdminController::class, 'register'])->name('admin.register');
        Route::post('/logout', [AuthAdminController::class, 'logout'])->name('admin.logout');
        Route::get('/getaccount', [AuthAdminController::class, 'getAccount'])->name('admin.getAccount');
});