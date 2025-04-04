<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});
Route::match(['get', 'post'], '/payment/acceptance/post_pay', function(Request $request) {
    return app()->make(\App\Http\Controllers\API\PaymentController::class)->handleCallback($request);
});
