<?php
namespace App\Http\Controllers\API;
use App\Models\{CartItem};
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
            $productPrice = \DB::table('mobiles')->where('id', $request->product_id)->value('price');
        } elseif ($request->product_type === 'accessory') {
            $productPrice = \DB::table('accessories')->where('id', $request->product_id)->value('price');
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
                'price' => $productPrice
            ]);
        }

        $cart->updateTotalPrice();
        return $this->sendSuccess('Product added/updated in cart', $cartItem, 201);
    }
    public function update(Request $request, CartItem $cartItem)
    {
        $user = auth('api')->user();
        if ($cartItem->cart->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized: You do not own this cart item'], 403);
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
