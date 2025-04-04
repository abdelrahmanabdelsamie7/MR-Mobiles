<?php

namespace App\Services;

use App\Models\{Order, OrderItem, Cart, User};
use App\Mail\PaymentSuccessMail;
use Illuminate\Support\Facades\{Log, Mail};

class OrderService
{
    public function createOrderFromCart(Cart $cart, User $user)
    {
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $cart->total_price,
            'status' => 'pending'
        ]);
        foreach ($cart->cartItems as $cartItem) {
            $product = $cartItem->product;
            if (!$product) {
                throw new \Exception('Invalid product in cart');
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_type' => $product instanceof Mobile ? 'mobile' : 'accessory',
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
                'product_color_id' => $product instanceof Mobile ? $cartItem->product_color_id : null
            ]);
        }
        Log::info('Order created successfully', [
            'order_id' => $order->id,
            'user_id' => $user->id,
            'total_price' => $cart->total_price
        ]);
        return $order;
    }
    public function updateOrderStatus(Order $order, string $status)
    {
        $order->update(['status' => $status]);
        Log::info('Order status updated', [
            'order_id' => $order->id,
            'new_status' => $status
        ]);

        // Send success email if order is completed
        if ($status === 'completed') {
            $this->sendPaymentSuccessEmail($order);
        }
    }
    public function clearUserCart(User $user)
    {
        $user->cart->cartItems()->delete();
        Log::info('User cart cleared', ['user_id' => $user->id]);
    }
    private function sendPaymentSuccessEmail(Order $order)
    {
        try {
            Mail::to($order->user->email)->send(new PaymentSuccessMail($order));
            Log::info('Payment success email sent', [
                'order_id' => $order->id,
                'user_email' => $order->user->email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment success email', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}