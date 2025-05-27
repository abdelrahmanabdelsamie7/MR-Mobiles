<?php
namespace App\Http\Controllers\API;
use App\Models\Mobile;
use App\Http\Resources\MobileResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\MobileRequest;
use App\Traits\{ResponseJsonTrait, UploadImageTrait};

class MobileController extends Controller
{
    use ResponseJsonTrait, UploadImageTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'update', 'destroy']);
    }
    public function index()
    {
        $mobiles = Mobile::with([
            'brand',
            'colorVariants.color',
            'colorVariants.images',
            'variantImages'
        ])->get();
        return $this->sendSuccess('All Mobiles Retrieved Successfully!', MobileResource::collection($mobiles));
    }
    public function show(string $id)
    {
        $mobile = Mobile::with([
            'brand',
            'colorVariants.color',
            'colorVariants.images',
            'variantImages'
        ])->findOrFail($id);
        return $this->sendSuccess('Mobile Data Retrieved Successfully!', new MobileResource($mobile));
    }
    public function store(MobileRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image_cover')) {
            $data['image_cover'] = $this->uploadImage($request->file('image_cover'), 'mobiles');
        }
        $mobile = Mobile::create($data);
        return $this->sendSuccess('Mobile Added Successfully', $mobile, 201);
    }
    public function update(MobileRequest $request, string $id)
    {
        $mobile = Mobile::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('image_cover')) {
            $this->deleteImage($mobile->image_cover);
            $data['image_cover'] = $this->uploadImage($request->file('image_cover'), 'mobiles');
        }

        $mobile->update($data);
        return $this->sendSuccess('Mobile Data Updated Successfully', $mobile, 200);
    }
    public function destroy($id)
    {
        $mobile = Mobile::findOrFail($id);
        $this->deleteImage($mobile->image_cover);
        $mobile->delete();
        return $this->sendSuccess('Mobile Deleted Successfully');
    }
}
