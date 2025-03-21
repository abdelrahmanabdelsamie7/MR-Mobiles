<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\traits\ResponseJsonTrait;
class CategoryController extends Controller
{
    use ResponseJsonTrait;
    public function index()
    {
        $categories = Category::all();
        return $this->sendSuccess('Categories Data Retrieved Successfully!', $categories);
    }
    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());
        return $this->sendSuccess('Category Created Successfully!', $category, 201);
    }
    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        return $this->sendSuccess('Category Data Retrieved Successfully!', $category);
    }
    public function update(CategoryRequest $request, string $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->validated());
        return $this->sendSuccess('Category Data Updated Successfully!', $category);
    }
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return $this->sendSuccess('Category Deleted Successfully!');
    }
}
