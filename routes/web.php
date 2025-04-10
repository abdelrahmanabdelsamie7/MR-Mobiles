<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});
Route::match(['get', 'post'], '/payment/acceptance/post_pay', function (Request $request) {
    return app()->make(\App\Http\Controllers\API\PaymentController::class)->handleCallback($request);
});

Route::get('/payment/success', function (Request $request) {
    return view('payment.success', [
        'order_id' => $request->query('order_id')
    ]);
})->name('payment.success');

Route::get('/payment/failed', function (Request $request) {
    return view('payment.failed', [
        'error_message' => $request->query('error_message'),
        'order_id' => $request->query('order_id')
    ]);
})->name('payment.failed');
