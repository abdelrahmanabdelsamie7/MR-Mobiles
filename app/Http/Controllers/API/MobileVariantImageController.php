<?php
namespace App\Http\Controllers\API;
use App\Models\MobileVariantImage;
use App\Traits\{ResponseJsonTrait, UploadImageTrait};
use App\Http\Controllers\Controller;
use App\Http\Requests\MobileImageRequest;
class MobileVariantImageController extends Controller
{
    use ResponseJsonTrait, UploadImageTrait;
    protected string $uploadFolder = 'mobiles';
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'destroy']);
    }
    public function store(MobileImageRequest $request)
    {
        $data = $request->validated();
        $images = $request->file('images');
        $createdImages = [];
        foreach ($images as $image) {
            $imageUrl = $this->uploadImage($image, $this->uploadFolder);
            $mobileImage = MobileVariantImage::create([
                'mobile_color_variant_id' => $data['mobile_color_variant_id'],
                'image' => $imageUrl,
            ]);
            $createdImages[] = $mobileImage;
        }
        return $this->sendSuccess('Images Added Successfully', $createdImages, 201);
    }
    public function destroy($id)
    {
        $mobile_image = MobileVariantImage::findOrFail($id);
        if ($mobile_image->image) {
            $this->deleteImage($mobile_image->image);
        }
        $mobile_image->delete();
        return $this->sendSuccess('Image Mobile Removed Successfully');
    }
}