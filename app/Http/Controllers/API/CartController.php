<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Traits\ResponseJsonTrait;

class CartController extends Controller
{
    use ResponseJsonTrait;
    protected $user;
    public function __construct()
    {
        $this->user = auth('api')->user();
    }
    public function store()
    {
        $cart = Cart::firstOrCreate(['user_id' => $this->user->id]);
        $cart->updateTotalPrice();
        return $this->sendSuccess('Cart Created Successfully!', $cart, 201);
    }
    public function index()
    {
        $cart = Cart::where('user_id', $this->user->id)
        ->with(['items.product', 'items.mobileColor', 'items.accessoryColor'])
            ->first();
        if (!$cart || optional($cart->items)->isEmpty()) {
            return $this->sendSuccess("Cart is empty!");
        }
        return $this->sendSuccess('Cart Retrieved Successfully!', $cart);
    }
    public function deleteItems()
    {
        $cart = Cart::where('user_id', $this->user->id)->firstOrFail();
        $cart->items()->delete();
        $cart->updateTotalPrice();
        return $this->sendSuccess('Cart items deleted successfully!');
    }
}