<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Order, CartItem, Cart};
use App\Traits\UploadImageTrait;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    use UploadImageTrait;

    public function index()
    {
        $orders = Order::where('user_id', auth('api')->user()->id)
            ->with(['items.color'])
            ->get();

        return response()->json($orders);
    }
    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:instapay,vodafone_cash,cod',
            'payment_proof' => 'nullable|image',
            'note' => 'nullable|string',
        ]);

        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $cartItems = $cart->items;

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $total = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
        $paymentProof = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProof = $this->uploadImage($request->file('payment_proof'), 'payment_proofs');
        }
        $order = Order::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'payment_proof' => $paymentProof,
            'note' => $request->note,
            'total_price' => $total,
        ]);
        $waPhone = '01129508321';
        $waMessage = 'عميل جديد عمل طلب، راجعه في لوحة التحكم: ' . $order->id;
        $waUrl = "https://wa.me/$waPhone?text=" . urlencode($waMessage);
        return response()->json([
            'message' => 'تم إنشاء الطلب بنجاح',
            'order_id' => $order->id,
            'whatsapp_url' => $waUrl,
        ]);
    }
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,confirmed,rejected',
        ]);
        $order->update([
            'payment_status' => $request->payment_status,
        ]);
        if ($request->payment_status === 'confirmed') {
            $cartItems = CartItem::where('cart_id', $order->cart_id)->get();
            foreach ($cartItems as $item) {
                $order->items()->create([
                    'id' => Str::uuid(),
                    'product_id' => $item->product_id,
                    'product_type' => $item->product_type,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'product_color_id' => $item->product_color_id,
                ]);
            }
            CartItem::where('cart_id', $order->cart_id)->delete();
        }
        return response()->json(['message' => 'تم تحديث حالة الطلب بنجاح']);
    }
}
