<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\traits\ResponseJsonTrait;
class CartController extends Controller
{
    use ResponseJsonTrait;
    public function index()
    {
        $user = auth('api')->user();
        $cart = $user->cart()->with('cartItems')->first();
        if (!$cart || $cart->cartItems->isEmpty()) {
            return $this->sendSuccess("Cart doesn't have any product , is empty!");
        }
        return $this->sendSuccess('Cart Retrieved Successfully!', $cart);
    }
    public function store()
    {
        $user = auth('api')->user();
        $cart = $user->cart()->firstOrCreate(['user_id' => $user->id]);
        return $this->sendSuccess('Cart Created Successfully!', $cart, 201);
    }
    public function deleteItems()
    {
        $user = auth('api')->user();
        $cart = $user->cart;
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }
        $cart->cartItems()->delete();
        return $this->sendSuccess('Cart items deleted successfully!');
    }
}
