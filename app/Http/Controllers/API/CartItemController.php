<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\{Cart, CartItem, MobileColorVariant, Accessory};
use App\traits\ResponseJsonTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class CartItemController extends Controller
{
    use ResponseJsonTrait;
    public function index()
    {
        $cart = $this->getUserCart();
        $items = $cart ? $cart->cartItems()->with('product', 'color')->get() : collect();
        return response()->json([
            'success' => true,
            'data' => $items,
            'message' => 'Cart items retrieved successfully.'
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|uuid',
            'product_type' => ['required', Rule::in(['mobile', 'accessory'])],
            'quantity' => 'required|integer|min:1',
            'product_color_id' => 'nullable|uuid'
        ]);

        $cart = $this->getUserCart(true);

        $productId = $request->product_id;
        $productType = $request->product_type;
        $quantity = $request->quantity;
        $colorId = $request->product_color_id;

        // جلب المنتج حسب النوع
        if ($productType === 'mobile') {
            if (!$colorId) {
                return response()->json(['success' => false, 'message' => 'Color variant is required for mobile products.'], 422);
            }
            $variant = MobileColorVariant::findOrFail($colorId);
            if ($variant->mobile_id !== $productId) {
                return response()->json(['success' => false, 'message' => 'Product ID and color variant mismatch.'], 422);
            }
            // تحقق من المخزون
            if ($variant->stock_quantity < $quantity) {
                return response()->json(['success' => false, 'message' => 'Insufficient stock for the selected color variant.'], 400);
            }
            $price = $variant->mobile->final_price;
        } else if ($productType === 'accessory') {
            $accessory = Accessory::findOrFail($productId);
            if ($accessory->stock_quantity < $quantity) {
                return response()->json(['success' => false, 'message' => 'Insufficient stock for accessory.'], 400);
            }
            $price = $accessory->final_price;
            $colorId = null; // accessories don't have color variants in this model
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid product type.'], 422);
        }

        // تحقق إذا العنصر موجود في الكارت بنفس المنتج واللون لتحديث الكمية فقط
        $existingItem = $cart->cartItems()
            ->where('product_id', $productId)
            ->where('product_type', $productType)
            ->where('product_color_id', $colorId)
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;
            // تحقق من المخزون قبل التحديث
            if ($productType === 'mobile' && $variant->stock_quantity < $newQuantity) {
                return response()->json(['success' => false, 'message' => 'Insufficient stock to increase quantity.'], 400);
            }
            if ($productType === 'accessory' && $accessory->stock_quantity < $newQuantity) {
                return response()->json(['success' => false, 'message' => 'Insufficient stock to increase quantity.'], 400);
            }
            $existingItem->update(['quantity' => $newQuantity]);
        } else {
            // إنشاء عنصر جديد
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'product_type' => $productType,
                'quantity' => $quantity,
                'price' => $price,
                'product_color_id' => $colorId,
            ]);
        }

        // تحديث اجمالي السعر والكمية
        $cart->updateTotalPrice();

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully.',
            'data' => $cart->load('cartItems')
        ]);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $cartItem = CartItem::findOrFail($id);
        $cart = $this->getUserCart();
        if (!$cart || $cart->id !== $cartItem->cart_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }
        $quantity = $request->quantity;
        if ($cartItem->product_type === 'mobile') {
            $variant = MobileColorVariant::findOrFail($cartItem->product_color_id);
            if ($variant->stock_quantity < $quantity) {
                return response()->json(['success' => false, 'message' => 'Insufficient stock for the selected variant.'], 400);
            }
        } else if ($cartItem->product_type === 'accessory') {
            $accessory = Accessory::findOrFail($cartItem->product_id);
            if ($accessory->stock_quantity < $quantity) {
                return response()->json(['success' => false, 'message' => 'Insufficient stock for accessory.'], 400);
            }
        }
        $cartItem->update(['quantity' => $quantity]);
        $cart->updateTotalPrice();
        return response()->json([
            'success' => true,
            'message' => 'Cart item updated successfully.',
            'data' => $cartItem
        ]);
    }
    public function destroy($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cart = $this->getUserCart();
        if (!$cart || $cart->id !== $cartItem->cart_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }
        $cartItem->delete();
        $cart->updateTotalPrice();
        return response()->json([
            'success' => true,
            'message' => 'Cart item removed successfully.'
        ]);
    }
    protected function getUserCart($createIfNotExist = false)
    {
        $user = auth('api')->user();
        if (!$user) {
            return $this->sendErrorResponse('Unauthorized', 401);
        }
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart && $createIfNotExist) {
            $cart = Cart::create(['user_id' => $user->id, 'total_price' => 0, 'total_quantity' => 0]);
        }
        return $cart;
    }
}
