<?php
namespace App\Http\Controllers\API;
use App\Models\Brand;
use App\traits\ResponseJsonTrait;
use App\Http\Requests\BrandRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class BrandController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'update', 'destroy']);
    }
    public function index()
    {
        $brands = Brand::all();
        return $this->sendSuccess('All Brands Retrieved Successfully!', $brands);
    }
    public function store(BrandRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $originalName = $request->file('image')->getClientOriginalName();
            $imageName = time() . '_' . $originalName;
            $request->file('image')->move(public_path('uploads/brands'), $imageName);
            $data['image'] = asset('uploads/brands/' . $imageName);
        } else {
            $data['image'] = null;
        }
        $brand = Brand::create($data);
        return $this->sendSuccess('Brand Added Successfully', $brand, 201);
    }
    public function show(string $id)
    {
        $brand = Brand::with(['mobiles', 'accessories'])->findOrFail($id);
        return $this->sendSuccess('Brand Data Retrieved Successfully!', $brand);
    }
    public function update(BrandRequest $request, string $id)
    {
        $brand = Brand::findOrFail($id);
        $data = $request->validated();
        if ($request->hasFile('image')) {
            if ($brand->image && file_exists(public_path('uploads/brands/' . basename($brand->image)))) {
                unlink(public_path('uploads/brands/' . basename($brand->image)));
            }
            $originalName = $request->file('image')->getClientOriginalName();
            $imageName = time() . '_' . $originalName;
            $request->file('image')->move(public_path('uploads/brands'), $imageName);
            $data['image'] = asset('uploads/brands/' . $imageName);
        }
        $brand->update($data);
        return $this->sendSuccess('Brand Updated Successfully', $brand, 200);
    }
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        if ($brand->image && !str_contains($brand->image, 'default.jpg')) {
            $imageName = basename($brand->image);
            $imagePath = public_path("uploads/brands/" . $imageName);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
        $brand->delete();
        return $this->sendSuccess('Brand Data Removed Successfully');
    }
}
