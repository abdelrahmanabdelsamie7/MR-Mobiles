<?php
namespace App\Http\Controllers\API;
use App\Models\Accessory;
use App\traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Requests\AccessoriesRequest;

class AccessoryController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store','update','destroy']);
    }
    public function index()
    {
        $accessories = Accessory::all();
        return $this->sendSuccess('Accessories Retrieved Successfully!', $accessories);
    }
    public function show(string $id)
    {
        $accessory = Accessory::with(['brand:id,name,image'])->findOrFail($id);
        return $this->sendSuccess('Accessory Data Retrieved Successfully!', $accessory);
    }
    public function store(AccessoriesRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $originalName = $request->file('image')->getClientOriginalName();
            $imageName = time() . '_' . $originalName;
            $request->file('image')->move(public_path('uploads/accessories'), $imageName);
            $data['image'] = asset('uploads/accessories/' . $imageName);
        }
        $accessory = Accessory::create($data);
        return $this->sendSuccess('Accessory Added Successfully', $accessory, 201);
    }
    public function update(AccessoriesRequest $request, string $id)
    {
        $accessory = Accessory::findOrFail($id);
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $oldImagePath = public_path('uploads/accessories/' . basename($accessory->image));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
            $originalName = $request->image->getClientOriginalName();
            $imageName = time() . '_' . $originalName;
            $request->image->move(public_path('uploads/accessories'), $imageName);
            $data['image'] = asset('uploads/accessories/' . $imageName);
        }
        $accessory->update($data);
        return $this->sendSuccess('Accessory Data Updated Successfully', $accessory, 200);
    }
    public function destroy($id)
    {
        $accessory = Accessory::findOrFail($id);
        if ($accessory->image && !str_contains($accessory->image, 'default.jpg')) {
            $imageName = basename($accessory->image);
            $imagePath = public_path("uploads/accessories/" . $imageName);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
        $accessory->delete();
        return $this->sendSuccess('Accessory Removed Successfully');
    }
}
