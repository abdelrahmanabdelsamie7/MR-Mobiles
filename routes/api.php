<?php

use App\Http\Controllers\API\{CategoryController,BrandController,MobileController,MobileColorController};
Route::apiResource('categories' , CategoryController::class);
Route::apiResource('brands' , BrandController::class);
Route::apiResource('mobiles' , MobileController::class);
Route::apiResource('mobile-colors' , MobileColorController::class);


Route::match(['post', 'put', 'patch'], 'brands/{id}', [BrandController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'mobiles/{id}', [MobileController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'mobile-colors/{id}', [MobileColorController::class, 'update']);
