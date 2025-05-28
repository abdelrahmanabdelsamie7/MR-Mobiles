<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\AccessoryVariantImage;
use App\Http\Requests\AccessoryImageRequest;
use App\Traits\{UploadImageTrait, ResponseJsonTrait};

class AccessoryVariantImageController extends Controller
{
    use ResponseJsonTrait, UploadImageTrait ;
    protected string $uploadFolder = 'accessories';
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'destroy']);
    }
    public function store(AccessoryImageRequest $request)
    {
        $data = $request->validated();
        $images = $request->file('images');
        $createdImages = [];
        foreach ($images as $image) {
            $imageUrl = $this->uploadImage($image, $this->uploadFolder);
            $accessoryImage = AccessoryVariantImage::create([
                'accessory_color_variant_id' => $data['accessory_color_variant_id'],
                'image' => $imageUrl,
            ]);
            $createdImages[] = $accessoryImage;
        }
        return $this->sendSuccess('Images Added Successfully', $createdImages, 201);
    }
    public function destroy($id)
    {
        $accessory_image = AccessoryVariantImage::findOrFail($id);
        if ($accessory_image->image) {
            $this->deleteImage($accessory_image->image);
        }
        $accessory_image->delete();
        return $this->sendSuccess('Image Accessory Removed Successfully');
    }
}
