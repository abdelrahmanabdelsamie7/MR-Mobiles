<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Requests\MobileColorRequest;
use App\Models\MobileColor;
use App\traits\ResponseJsonTrait;
use Illuminate\Support\Facades\File;
class MobileColorController extends Controller
{
    use ResponseJsonTrait;
    public function store(MobileColorRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $originalName = $request->file('image')->getClientOriginalName();
            $imageName = time() . '_' . $originalName;
            $request->file('image')->move(public_path('uploads/mobiles'), $imageName);
            $data['image'] = asset('uploads/mobiles/' . $imageName);
        }

        $mobile_color = MobileColor::create($data);
        return $this->sendSuccess('Image & Color Mobile Added Successfully', $mobile_color, 201);
    }
    public function update(MobileColorRequest $request, string $id)
    {
        $mobile_color = MobileColor::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $oldImagePath = public_path('uploads/mobiles/' . basename($mobile_color->image));
            if ($mobile_color->image && File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            $originalName = $request->file('image')->getClientOriginalName();
            $imageName = time() . '_' . $originalName;
            $request->file('image')->move(public_path('uploads/mobiles'), $imageName);
            $data['image'] = asset('uploads/mobiles/' . $imageName);
        }

        $mobile_color->update($data);
        return $this->sendSuccess('Image & Color Mobile Data Updated Successfully', $mobile_color, 200);
    }
    public function destroy($id)
    {
        $mobile_color = MobileColor::findOrFail($id);

        if ($mobile_color->image && basename($mobile_color->image) !== 'default.jpg') {
            $imagePath = public_path("uploads/mobiles/" . basename($mobile_color->image));
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        $mobile_color->delete();
        return $this->sendSuccess('Image & Color Mobile Removed Successfully');
    }
}
