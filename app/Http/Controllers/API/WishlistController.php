<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Requests\WishlistRequest;
use App\Models\Wishlist;
use App\traits\ResponseJsonTrait;
class WishlistController extends Controller
{
    use ResponseJsonTrait;
    public function index()
    {
        if (!auth('api')->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $userId = auth('api')->id();
        $wishlists = Wishlist::where('user_id', $userId)->with('product')->get();
        return $this->sendSuccess('My Wishlist Retrieved Successfully !', $wishlists);
    }
    public function store(WishlistRequest $request)
    {
        if (!auth('api')->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $userId = auth('api')->id();
        if (Wishlist::where(['user_id' => $userId, 'product_id' => $request->product_id, 'product_type' => $request->product_type])->exists()) {
            return response()->json(['message' => 'Product already in wishlist'], 409);
        }
        $wishlist = Wishlist::create([
            'user_id' => $userId,
            'product_id' => $request->product_id,
            'product_type' => $request->product_type,
        ]);
        return $this->sendSuccess('Product Added to Wishlist Successfully !', $wishlist , 201);
    }
    public function destroy($id)
    {
        if (!auth('api')->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $userId = auth('api')->id();
        $wishlist = Wishlist::where('id', $id)->where('user_id', $userId)->first();
        if (!$wishlist) {
            return response()->json(['message' => 'Wishlist item not found'], 404);
        }
        $wishlist->delete();
        return $this->sendSuccess('Product Removed from Wishlist Successfully !');
    }
}