<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\traits\ResponseJsonTrait;
use App\Models\MobileColorVariant;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Requests\MobileColorVariantRequest;

class MobileColorVariantController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store','update','destroy']);
    }
    public function store(MobileColorVariantRequest $request)
    {
        $mobile_color_variant = MobileColorVariant::create($request->validated());
        return $this->sendSuccess('Color & it is quantity added to Mobile Successfully', $mobile_color_variant, 201);
    }
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'stock_quantity' => 'required|integer|min:0',
        ]);
        $mobile_color_variant = MobileColorVariant::findOrFail($id);
        $mobile_color_variant->stock_quantity = $validated['stock_quantity'];
        $mobile_color_variant->save();
        return $this->sendSuccess('Mobile Variant Quantity Updated Successfully', $mobile_color_variant, 200);
    }
    public function destroy($id)
    {
        $mobile_color_variant = MobileColorVariant::findOrFail($id);

        if ($mobile_color_variant->image && basename($mobile_color_variant->image) !== 'default.jpg') {
            $imagePath = public_path("uploads/mobiles/" . basename($mobile_color_variant->image));
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        $mobile_color_variant->delete();
        return $this->sendSuccess('Image Of Color Mobile Removed Successfully');
    }
}
