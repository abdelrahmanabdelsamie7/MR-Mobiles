<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Order, Cart};
use App\Traits\{UploadImageTrait, ResponseJsonTrait};
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusMail;
use Illuminate\Support\Str;
class OrderController extends Controller
{
    use UploadImageTrait, ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['index', 'update']);
        $this->middleware('auth:api')->only(['store', 'userOrders']);
    }
    public function index()
    {
        $orders = Order::with([
            'user',
            'user.cart',
            'items.mobileColor.mobile',
            'items.accessoryColor.accessory'
        ])
        ->orderBy('created_at', 'desc')
        ->get();
        return $this->sendSuccess('All Orders Retrieved Successfully!', OrderResource::collection($orders));
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
            return $this->sendError('Unauthorized', 401);
        }
        $cart = Cart::where('user_id', $user->id)->first();
        if (!$cart) {
            return $this->sendError('Cart not found', 404);
        }
        $cartItems = $cart->items;
        if ($cartItems->isEmpty()) {
            return $this->sendError('Cart is empty', 400);
        }
        $total = $cartItems->sum(fn($item) => $item->price * $item->quantity);
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
        Mail::to($user->email)->send(new OrderStatusMail($order, 'created'));
        $waPhone = '01129508321';
        $waMessage = 'أنا طلبت الأوردر ده وعايزك تراجع البيانات وترد في أقرب وقت ممكن: ' . $order->id;
        $waUrl = "https://wa.me/{$waPhone}?text=" . urlencode($waMessage);
        return $this->sendSuccess('Your Order Send Successfully !', [
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
            Mail::to($order->user->email)->send(new OrderStatusMail($order, 'confirmed'));
        } elseif ($request->payment_status === 'rejected') {
            Mail::to($order->user->email)->send(new OrderStatusMail($order, 'rejected'));
        }
        return $this->sendSuccess('تم تحديث حالة الطلب بنجاح');
    }
    public function userOrders()
    {
        $user = auth('api')->user();
        $orders = $user->orders()
            ->where('payment_status', 'confirmed')
            ->with([
                'items.mobileColor.mobile',
                'items.accessoryColor.accessory'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendSuccess('User Orders Retrieved Successfully!', OrderResource::collection($orders));
    }
}