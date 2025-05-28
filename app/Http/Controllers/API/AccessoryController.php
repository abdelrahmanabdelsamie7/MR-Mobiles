<?php
namespace App\Http\Controllers\API;
use App\Http\Resources\AccessoryResource;
use App\Models\Accessory;
use App\Traits\{ResponseJsonTrait, UploadImageTrait};
use App\Http\Controllers\Controller;
use App\Http\Requests\AccessoriesRequest;
class AccessoryController extends Controller
{
    use ResponseJsonTrait, UploadImageTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'update', 'destroy']);
    }
    public function index()
    {
        $accessories = Accessory::with([
            'brand',
            'colorVariants.color',
            'colorVariants.images',
            'variantImages'
        ])->get();
        return $this->sendSuccess('Accessories Retrieved Successfully!', AccessoryResource::collection($accessories));
    }
    public function show(string $id)
    {
        $accessory = Accessory::with([
            'brand',
            'colorVariants.color',
            'colorVariants.images',
            'variantImages'
        ])->findOrFail($id);
        return $this->sendSuccess('Accessory Data Retrieved Successfully!', new AccessoryResource($accessory));
    }
    public function store(AccessoriesRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), 'accessories');
        }
        $accessory = Accessory::create($data);
        return $this->sendSuccess('Accessory Added Successfully', $accessory, 201);
    }
    public function update(AccessoriesRequest $request, string $id)
    {
        $accessory = Accessory::findOrFail($id);
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $this->deleteImage($accessory->image);
            $data['image'] = $this->uploadImage($request->file('image'), 'accessories');
        }
        $accessory->update($data);
        return $this->sendSuccess('Accessory Data Updated Successfully', $accessory, 200);
    }
    public function destroy($id)
    {
        $accessory = Accessory::findOrFail($id);
        $this->deleteImage($accessory->image);
        $accessory->delete();
        return $this->sendSuccess('Accessory Removed Successfully');
    }
}