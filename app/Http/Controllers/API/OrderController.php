<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\ResponseJsonTrait;
use Illuminate\Support\Facades\Auth;
class OrderController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->get();
        return $this->sendSuccess('Orders retrieved successfully', $orders);
    }
    public function show($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->with('orderItems.product')
            ->first();
        if (!$order) {
            return $this->sendError('Order not found', 404);
        }
        return $this->sendSuccess('Order retrieved successfully', $order);
    }
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