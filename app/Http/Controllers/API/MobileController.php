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
    public function index()
    {
        $mobiles = Mobile::all();
        return $this->sendSuccess('All Mobiles Retrieved Successfully!', $mobiles);
    }
    public function show(string $id)
    {
        $mobile = Mobile::with(['brand:id,name,image', 'category:id,name', 'colors', 'images'])
            ->findOrFail($id);

        return $this->sendSuccess('Mobile Retrieved Successfully!', $mobile);
    }
    public function store(MobileRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image_cover')) {
            $originalName = $request->file('image_cover')->getClientOriginalName();
            $imageName = time() . '_' . $originalName;
            $request->file('image_cover')->move(public_path('uploads/mobiles'), $imageName);
            $data['image_cover'] = asset('uploads/mobiles/' . $imageName);
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
            $originalName = $request->image_cover->getClientOriginalName();
            $imageName = time() . '_' . $originalName;
            $request->image_cover->move(public_path('uploads/mobiles'), $imageName);
            $data['image_cover'] = asset('uploads/mobiles/' . $imageName);
        }
        $mobile->update($data);
        return $this->sendSuccess('Mobile Data Updated Successfully', $mobile, 200);
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