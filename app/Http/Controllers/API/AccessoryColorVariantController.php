<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Models\AccessoryColorVariant;
use App\Http\Requests\AccessoryColorVariantRequest;

class AccessoryColorVariantController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store','update','destroy']);
    }
    public function store(AccessoryColorVariantRequest $request)
    {
        $accessory_color_variant = AccessoryColorVariant::create($request->validated());
        return $this->sendSuccess('Color & it is quantity added to Accessory Successfully', $accessory_color_variant, 201);
    }
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'stock_quantity' => 'required|integer|min:0',
        ]);
        $accessory_color_variant = accessoryColorVariant::findOrFail($id);
        $accessory_color_variant->stock_quantity = $validated['stock_quantity'];
        $accessory_color_variant->save();
        return $this->sendSuccess('Accessory Variant Quantity Updated Successfully', $accessory_color_variant, 200);
    }
    public function destroy($id)
    {
        $accessory_color_variant = accessoryColorVariant::findOrFail($id);
        if ($accessory_color_variant->image && basename($accessory_color_variant->image) !== 'default.jpg') {
            $imagePath = public_path("uploads/accessories/" . basename($accessory_color_variant->image));
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
        $accessory_color_variant->delete();
        return $this->sendSuccess('Image Of Color Accessory Removed Successfully');
    }
}