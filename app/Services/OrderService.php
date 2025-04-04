<?php
namespace App\Services;
use App\Mail\PaymentSuccessMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\{Log};
use App\Models\{Order, OrderItem, Cart, User, Mobile, Accessory};
use Illuminate\Support\Facades\DB;
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
        $order->load(['orderItems.product', 'user']);
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
        if ($status === 'completed') {
            $order->load(['orderItems.product', 'user']);
            $this->sendPaymentSuccessEmail($order);
            $this->updateProductQuantities($order);
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
    private function updateProductQuantities(Order $order)
    {
        Log::info('Starting product quantity update', [
            'order_id' => $order->id,
            'order_items_count' => $order->orderItems->count()
        ]);
        DB::beginTransaction();
        try {
            foreach ($order->orderItems as $item) {
                 $product = $item->product_type === 'mobile'
                    ? Mobile::lockForUpdate()->find($item->product_id)
                    : Accessory::lockForUpdate()->find($item->product_id);

                if (!$product) {
                    Log::error('Product not found for order item', [
                        'order_id' => $order->id,
                        'order_item_id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_type' => $item->product_type
                    ]);
                    throw new \Exception("Product not found for order item {$item->id}");
                }

                $oldQuantity = $product->stock_quantity;

                // Check if there's enough stock before updating
                if ($oldQuantity < $item->quantity) {
                    Log::error('Insufficient stock for product', [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_type' => $item->product_type,
                        'requested_quantity' => $item->quantity,
                        'available_quantity' => $oldQuantity
                    ]);
                    throw new \Exception("Insufficient stock for product {$product->id}. Requested: {$item->quantity}, Available: {$oldQuantity}");
                }

                $newQuantity = $oldQuantity - $item->quantity;
                $isOutOfStock = $newQuantity <= 0;
                $product->stock_quantity = $newQuantity;
                $product->status = $isOutOfStock ? 'out_of_stock' : $product->status;
                $product->save();
                Log::info('Product quantity updated successfully', [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_type' => $item->product_type,
                    'quantity_reduced' => $item->quantity,
                    'old_quantity' => $oldQuantity,
                    'new_quantity' => $newQuantity,
                    'is_out_of_stock' => $isOutOfStock
                ]);
            }
            DB::commit();
            Log::info('All product quantities updated successfully', ['order_id' => $order->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update product quantities', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}