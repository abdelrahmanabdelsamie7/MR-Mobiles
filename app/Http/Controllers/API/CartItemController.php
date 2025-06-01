<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\{Cart, CartItem, MobileColorVariant, Accessory, AccessoryColorVariant};
use App\traits\ResponseJsonTrait;
use App\Http\Requests\CartItemRequest;
use App\Http\Resources\CartResource;
use Illuminate\Http\Request;
class CartItemController extends Controller
{
    use ResponseJsonTrait;
    public function index()
    {
        $cart = $this->getUserCart();
        if (!$cart) {
            return $this->sendError('Cart not found', 404);
        }
        return $this->sendSuccess('Cart items retrieved successfully.', new CartResource($cart));
    }
    public function store(CartItemRequest $request)
    {
        $validated = $request->validated();
        $cart = $this->getUserCart(true);
        if (!$cart) {
            return $this->sendError('Unauthorized', 401);
        }

        $productId = $validated['product_id'];
        $productType = $validated['product_type'];
        $quantity = $validated['quantity'];
        $colorId = $validated['product_color_id'] ?? null;
        $price = 0;
        if ($productType === 'mobile') {
            if (!$colorId) {
                return $this->sendError('Color variant is required for mobile products.', 422);
            }
            $variant = MobileColorVariant::findOrFail($colorId);
            if ($variant->mobile_id !== $productId) {
                return $this->sendError('Product ID and color variant mismatch.', 422);
            }
            if ($variant->stock_quantity < $quantity) {
                return $this->sendError('Insufficient stock for the selected color variant.', 400);
            }
            $price = $variant->mobile->final_price;
        } elseif ($productType === 'accessory') {
            if (!$colorId) {
                return $this->sendError('Color variant is required for accessory products.', 422);
            }
            $variant = AccessoryColorVariant::findOrFail($colorId);

            if ($variant->accessory_id !== $productId) {
                return $this->sendError('Product ID and color variant mismatch for accessory.', 422);
            }

            if ($variant->stock_quantity < $quantity) {
                return $this->sendError('Insufficient stock for the selected accessory color variant.', 400);
            }
            $price = $variant->accessory->final_price;
        }
        $existingItem = $cart->items()
            ->where('product_id', $productId)
            ->where('product_type', $productType)
            ->where('product_color_id', $colorId)
            ->first();
        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;
            if (!$this->checkStock($productType, $productId, $colorId, $newQuantity)) {
                return $this->sendError('Insufficient stock to increase quantity.', 400);
            }
            $existingItem->update(['quantity' => $newQuantity]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'product_type' => $productType,
                'quantity' => $quantity,
                'price' => $price,
                'product_color_id' => $colorId,
            ]);
        }
        $cart->updateTotalPrice();
        return $this->sendSuccess('Product added to cart successfully.', $cart->load('items'));
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $cartItem = CartItem::findOrFail($id);
        $cart = $this->getUserCart();
        if (!$cart || $cart->id !== $cartItem->cart_id) {
            return $this->sendError('Unauthorized.', 403);
        }
        $newQuantity = $validated['quantity'];
        if (!$this->checkStock($cartItem->product_type, $cartItem->product_id, $cartItem->product_color_id, $newQuantity)) {
            return $this->sendError('Insufficient stock for this product.', 400);
        }
        $cartItem->update(['quantity' => $newQuantity]);
        $cart->updateTotalPrice();
        return $this->sendSuccess('Cart item updated successfully.', $cartItem);
    }
    public function destroy($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cart = $this->getUserCart();
        if (!$cart || $cart->id !== $cartItem->cart_id) {
            return $this->sendError('Unauthorized.', 403);
        }
        $cartItem->delete();
        $cart->updateTotalPrice();
        return $this->sendSuccess('Cart item removed successfully.');
    }
    protected function getUserCart($createIfNotExist = false)
    {
        $user = auth('api')->user();
        if (!$user) {
            return $this->sendError('Unauthorized', 401);
        }
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id],
            ['total_price' => 0, 'total_quantity' => 0]
        );
        return $cart;
    }
    protected function checkStock($type, $productId, $colorId, $quantity): bool
    {
        if ($type === 'mobile') {
            $variant = MobileColorVariant::find($colorId);
            return $variant && $variant->stock_quantity >= $quantity;
        }
        if ($type === 'accessory') {
            $variant = AccessoryColorVariant::find($colorId);
            return $variant && $variant->accessory_id === $productId && $variant->stock_quantity >= $quantity;
        }
        return false;
    }
}