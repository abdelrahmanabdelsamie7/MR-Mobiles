<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class MobileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $mobileId = $this->route('id') ?? $this->route('mobile');
        return [
            'title' => 'required|string|max:255|unique:mobiles,title,' . $mobileId,
            'brand_id' => 'required|exists:brands,id',
            'model_number' => 'required|string|max:100',
            'description' => 'nullable|string',
            'battery' => 'required|integer|min:1000|max:10000',
            'processor' => 'required|string|max:255',
            'storage' => 'required|string|max:255',
            'display' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|integer|min:0|max:100',
            'operating_system' => 'required|string|max:255',
            'camera' => 'nullable|string|max:255',
            'network_support' => 'required|string|max:255',
            'release_year' => 'required|integer|min:2000|max:' . date('Y'),
            'image_cover' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4048',
            'status' => 'required|in:available,out_of_stock,coming_soon',
        ];
    }
}