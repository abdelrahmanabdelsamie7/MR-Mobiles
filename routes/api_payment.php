<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PaymentController;
// Route::prefix('payment')->group(function () {
//     Route::post('/create-checkout-session', [PaymentController::class, 'createCheckoutSession'])->middleware('auth:api');
//     Route::get('/success', [PaymentController::class, 'success'])->name('payment.success')->withoutMiddleware(['auth:api']);
//     Route::get('/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel')->withoutMiddleware(['auth:api']);
// });
// Debug route
// Route::get('/api/debug/payment', function (Request $request) {
//     \Log::channel('paymob')->info('Debug Payment Callback:', [
//         'method' => $request->method(),
//         'data' => $request->all(),
//         'query' => $request->query(),
//         'headers' => $request->headers->all()
//     ]);
//     return response()->json(['status' => 'debug', 'data' => $request->all()]);
// })->withoutMiddleware(['auth:api', 'throttle:api']);