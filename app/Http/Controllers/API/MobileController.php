<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Requests\MobileRequest;
use App\Models\Mobile;
use App\traits\ResponseJsonTrait;
use Illuminate\Support\Facades\File;
class MobileController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'update', 'destroy']);
    }
    public function index()
    {
        $mobiles = Mobile::all();
        return $this->sendSuccess('All Mobiles Retrieved Successfully!', $mobiles);
    }
    public function show(string $id)
    {
        $mobile = Mobile::with(['colors' ,'images'])->findOrFail($id);
        return $this->sendSuccess('Mobile Data Retrieved Successfully!', $mobile);
    }
    public function store(MobileRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image_cover')) {
            $data['image_cover'] = $this->uploadImage($request->file('image_cover'));
        }
        $mobile = Mobile::create($data);
        return $this->sendSuccess('Mobile Added Successfully', $mobile, 201);
    }
    public function update(MobileRequest $request, string $id)
    {
        $mobile = Mobile::findOrFail($id);
        $data = $request->validated();
        if ($request->hasFile('image_cover')) {
            $oldImagePath = public_path('uploads/mobiles/' . basename($mobile->image_cover));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
            $data['image_cover'] = $this->uploadImage($request->file('image_cover'));
        }
        $mobile->update($data);
        return $this->sendSuccess('Mobile Data Updated Successfully', $mobile, 200);
    }
    private function uploadImage($image)
    {
        if ($image) {
            $imageName = uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/mobiles'), $imageName);
            return asset('uploads/mobiles/' . $imageName);
        }
        return null;
    }
    public function destroy($id)
    {
        $mobile = Mobile::findOrFail($id);
        if ($mobile->image_cover && !str_contains($mobile->image_cover, 'default.jpg')) {
            $imageName = basename($mobile->image_cover);
            $imagePath = public_path("uploads/mobiles/" . $imageName);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
        $mobile->delete();
        return $this->sendSuccess('Mobile Removed Successfully');
    }
}