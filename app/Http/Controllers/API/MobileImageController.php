<?php
namespace App\Http\Controllers\API;
use App\Models\MobileImage;
use App\traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Requests\MobileImageRequest;

class MobileImageController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store','update','destroy']);
    }
    public function store(MobileImageRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $originalName = $request->file('image')->getClientOriginalName();
            $imageName = time() . '_' . $originalName;
            $request->file('image')->move(public_path('uploads/mobiles'), $imageName);
            $data['image'] = asset('uploads/mobiles/' . $imageName);
        }
        $mobile_image = MobileImage::create($data);
        return $this->sendSuccess('Image Mobile Added Successfully', $mobile_image, 201);
    }
    public function update(MobileImageRequest $request, string $id)
    {
        $mobile_image = MobileImage::findOrFail($id);
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $oldImagePath = public_path('uploads/mobiles/' . basename($mobile_image->image));
            if ($mobile_image->image && File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }
            $originalName = $request->file('image')->getClientOriginalName();
            $imageName = time() . '_' . $originalName;
            $request->file('image')->move(public_path('uploads/mobiles'), $imageName);
            $data['image'] = asset('uploads/mobiles/' . $imageName);
        }
        $mobile_image->update($data);
        return $this->sendSuccess('Image Mobile Data Updated Successfully', $mobile_image, 200);
    }
    public function destroy($id)
    {
        $mobile_image = MobileImage::findOrFail($id);

        if ($mobile_image->image && basename($mobile_image->image) !== 'default.jpg') {
            $imagePath = public_path("uploads/mobiles/" . basename($mobile_image->image));
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
        $mobile_image->delete();
        return $this->sendSuccess('Image Mobile Removed Successfully');
    }
}
