<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\ResponseJsonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    use ResponseJsonTrait;

    public function __construct()
    {
        $this->middleware('auth:api'); // تأكد من أن المستخدم مسجل دخول
    }

    // ✅ جلب الطلبات الخاصة بالمستخدم الحالي فقط
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->get();
        return $this->sendSuccess('Orders retrieved successfully', $orders);
    }

    // ✅ جلب طلب معين بشرط أن يكون خاص بالمستخدم الحالي
    public function show($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->with('orderItems')->first();

        if (!$order) {
            return $this->sendError('Order not found', 404);
        }

        return $this->sendSuccess('Order retrieved successfully', $order);
    }

    // ✅ حذف الطلب بشرط أن يكون مملوكًا للمستخدم الحالي
    public function destroy($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$order) {
            return $this->sendError('Order not found or unauthorized', 404);
        }

        $order->delete();
        return $this->sendSuccess('Order deleted successfully');
    }
}