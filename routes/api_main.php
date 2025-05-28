<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{
    BrandController, MobileController, MobileColorVariantController, MobileVariantImageController,
    AccessoryController, WishlistController, CartController, CartItemController, ContactController, OrderController,
    StatisticsController, ColorController ,AccessoryColorVariantController , AccessoryVariantImageController
};
Route::apiResources([
    'brands' => BrandController::class,
    'mobiles' => MobileController::class,
    'mobile-colors' => MobileColorVariantController::class,
    'accessory-colors' => AccessoryColorVariantController::class,
    'mobile-images' => MobileVariantImageController::class,
    'accessory-images' => AccessoryVariantImageController::class,
    'accessories' => AccessoryController::class,
    'wishlist' => WishlistController::class,
    'cart' => CartController::class,
    'cart-items' => CartItemController::class,
    'orders' => OrderController::class,
    'contact-us' => ContactController::class,
    'colors' => ColorController::class,
]);

Route::post('/contact-us/{id}/reply', [ContactController::class, 'reply'])->middleware('auth:admins');
Route::get('statistics', [StatisticsController::class, 'getStatistics']);
Route::delete('cart', [CartController::class, 'deleteItems']);


$updateRoutes = [
    ['mobiles', MobileController::class],
    ['mobile-images', MobileVariantImageController::class],
    ['accessories', AccessoryController::class],
    ['brands', BrandController::class],
];
foreach ($updateRoutes as [$uri, $controller]) {
    Route::match(['post', 'put', 'patch'], "$uri/{id}", [$controller, 'update']);
}