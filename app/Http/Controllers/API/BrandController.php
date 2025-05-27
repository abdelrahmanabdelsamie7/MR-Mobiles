<?php
namespace App\Http\Controllers\API;
use App\Models\Brand;
use App\Traits\{ResponseJsonTrait, UploadImageTrait};
use App\Http\Requests\BrandRequest;
use App\Http\Controllers\Controller;
class BrandController extends Controller
{
    use ResponseJsonTrait, UploadImageTrait;
    protected string $uploadFolder = 'brands';
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
            $data['image'] = $this->uploadImage($request->file('image'), $this->uploadFolder);
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
            if ($brand->image) {
                $this->deleteImage($brand->image);
            }
            $data['image'] = $this->uploadImage($request->file('image'), $this->uploadFolder);
        }
        $brand->update($data);
        return $this->sendSuccess('Brand Updated Successfully', $brand, 200);
    }
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        if ($brand->image) {
            $this->deleteImage($brand->image);
        }
        $brand->delete();
        return $this->sendSuccess('Brand Data Removed Successfully');
    }
}
