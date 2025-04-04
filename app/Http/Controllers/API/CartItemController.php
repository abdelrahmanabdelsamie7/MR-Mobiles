<?php
namespace App\Http\Controllers\API;
use App\Models\CartItem;
use App\Models\Mobile;
use App\Models\Accessory;
use App\traits\ResponseJsonTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CartItemRequest;

class CartItemController extends Controller
{
    use ResponseJsonTrait;
    public function store(CartItemRequest $request)
    {
        $user = auth('api')->user();
        $cart = $user->cart;
        $productPrice = null;
        if ($request->product_type === 'mobile') {
            if (!$request->product_color_id) {
                return $this->sendError('Product color is required for mobile products.', 400);
            }
            $mobile = Mobile::find($request->product_id);
            $productPrice = $mobile ? $mobile->final_price : null;
            if ($mobile && $mobile->stock_quantity < $request->quantity) {
                return $this->sendError('Not enough stock available. Only ' . $mobile->stock_quantity . ' items left.', 400);
            }
        } elseif ($request->product_type === 'accessory') {
            $accessory = Accessory::find($request->product_id);
            $productPrice = $accessory ? $accessory->final_price : null;
            if ($accessory && $accessory->stock_quantity < $request->quantity) {
                return $this->sendError('Not enough stock available. Only ' . $accessory->stock_quantity . ' items left.', 400);
            }
        }
        if (!$productPrice) {
            return $this->sendError('Product price not found', 404);
        }
        $cartItem = CartItem::where([
            'cart_id' => $cart->id,
            'product_id' => $request->product_id,
            'product_type' => $request->product_type
        ])->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity);
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'product_type' => $request->product_type,
                'quantity' => $request->quantity,
                'price' => $productPrice,
                'product_color_id' => $request->product_color_id,
            ]);
        }
        $cart->updateTotalPrice();
        return $this->sendSuccess('Product added/updated in cart', $cartItem, 201);
    }
    public function update(Request $request, CartItem $cartItem)
    {
        $user = auth('api')->user();
        if ($cartItem->cart->user_id !== $user->id) {
            return $this->sendError('Unauthorized: You do not own this cart item', 403);
        }
        $request->validate(['quantity' => 'required|integer|min:1']);
        $cartItem->update(['quantity' => $request->quantity]);
        $cartItem->cart->updateTotalPrice();
        return $this->sendSuccess('Product quantity updated', $cartItem);
    }
    public function destroy(CartItem $cartItem)
    {
        $user = auth('api')->user();
        if ($cartItem->cart->user_id !== $user->id) {
            return $this->sendError('Unauthorized: You do not own this cart item', 403);
        }
        $cart = $cartItem->cart;
        $cartItem->delete();
        $cart->updateTotalPrice();
        return $this->sendSuccess('Product removed from cart');
    }
}