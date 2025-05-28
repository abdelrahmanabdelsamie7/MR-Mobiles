<?php
namespace App\Models;
use App\Models\{OrderItem, User, MobileColorVariant, AccessoryColorVariant};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, UsesUuid;
    protected $fillable = [
        'user_id',
        'payment_method',
        'payment_status',
        'payment_proof',
        'total_price',
        'note',
    ];
    protected static function booted()
    {
        static::updated(function ($order) {
            if ($order->isDirty('payment_status') && $order->payment_status === 'confirmed') {
                $order->moveCartToOrderItems();
            }
        });
    }
    public function moveCartToOrderItems()
    {
        $cart = $this->user->cart()->with('items')->first();
        if (!$cart || $cart->items->isEmpty()) {
            return;
        }
        foreach ($cart->items as $cartItem) {
            $this->items()->create([
                'product_id' => $cartItem->product_id,
                'product_type' => $cartItem->product_type,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
                'product_color_id' => $cartItem->product_color_id,
            ]);
            if ($cartItem->product_type === 'mobile') {
                $variant = MobileColorVariant::find($cartItem->product_color_id);
                if ($variant) {
                    $variant->decrement('stock_quantity', $cartItem->quantity);
                }
            } elseif ($cartItem->product_type === 'accessory') {
                $variant = AccessoryColorVariant::find($cartItem->product_color_id);
                if ($variant) {
                    $variant->decrement('stock_quantity', $cartItem->quantity);
                }
            }
        }
        $cart->items()->delete();
        $cart->update([
            'total_price' => 0,
            'total_quantity' => 0,
        ]);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}