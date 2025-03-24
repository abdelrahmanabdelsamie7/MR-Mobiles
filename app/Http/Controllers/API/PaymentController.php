<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\{Cart, Payment, Order, OrderItem};
use App\Mail\PaymentSuccessMail;
use App\Traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
class PaymentController extends Controller
{
    use ResponseJsonTrait;
    public function createCheckoutSession(Request $request)
    {
        if (!$request->has('cart_id')) {
            return $this->sendError('Cart ID is required', 400);
        }
        $cart = Cart::find($request->cart_id);
        if (!$cart) {
            return $this->sendError('Cart not found', 404);
        }
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'egp',
                        'product_data' => ['name' => 'Order #' . $cart->id],
                        'unit_amount' => $cart->total_price * 100,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'metadata' => [
                'cart_id' => $cart->id,
            ],
            'success_url' => env('APP_URL') . '/api/payment/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => env('APP_URL') . '/api/payment/cancel',
        ]);
        return response()->json(['url' => $checkout_session->url]);
    }
    public function success(Request $request)
    {
        if (!$request->has('session_id')) {
            return $this->sendError('Session ID is required', 400);
        }
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $session = Session::retrieve($request->session_id);
        $payment_intent_id = $session->payment_intent;
        if (!$payment_intent_id) {
            return $this->sendError('Payment Intent not found', 400);
        }
        $payment_intent = PaymentIntent::retrieve($payment_intent_id);
        $customer_email = $payment_intent->charges->data[0]->billing_details->email
            ?? $payment_intent->receipt_email
            ?? $session->customer_details->email
            ?? null;
        if (!$customer_email || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
            return $this->sendError('Invalid email address', 400);
        }
        $cart_id = $session->metadata->cart_id ?? null;
        if (!$cart_id) {
            return $this->sendError('Cart ID not found in metadata', 400);
        }
        $cart = Cart::with('cartItems.product')->find($cart_id);
        if (!$cart) {
            return $this->sendError('Cart not found', 404);
        }
        if ($cart->cartItems->isEmpty()) {
            return $this->sendError('Cart is empty', 400);
        }
        $order = Order::create([
            'user_id' => $cart->user_id,
            'total_price' => $cart->total_price,
            'status' => 'completed'
        ]);
        foreach ($cart->cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'product_type' => $cartItem->product_type,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
            ]);
        }
        Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'credit_card',
            'transaction_id' => $payment_intent_id,
            'amount' => $cart->total_price,
            'status' => 'completed',
        ]);
        $cart->cartItems()->delete();
        $cart->update(['total_price' => 0]);
        Mail::to($customer_email)->send(new PaymentSuccessMail($order->total_price, $order->id));
        return $this->sendSuccess('Payment successful and order created!', ['order_id' => $order->id]);
    }
    public function cancel()
    {
        return $this->sendSuccess('Payment cancelled', 200);
    }
}