<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Requests\ColorRequest;
use App\Models\Color;
use App\traits\ResponseJsonTrait;
class ColorController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['store', 'update', 'destroy']);
    }
    public function index()
    {
        $colors = Color::all();
        return $this->sendSuccess('All Avaliable Colors Retrieved Successfully!', $colors);
    }
    public function store(ColorRequest $request)
    {
        $color = Color::create($request->validated());
        return $this->sendSuccess('New Color Added Successfully', $color, 201);
    }
    public function update(ColorRequest $request, string $id)
    {
        $color = Color::findOrFail($id);
        $color->update($request->validated());
        return $this->sendSuccess('Color Data Updated Successfully', $color, 200);
    }
    public function destroy($id)
    {
        $color = Color::findOrFail($id);
        $color->delete();
        return $this->sendSuccess('Color Deleted Successfully');
    }
}
