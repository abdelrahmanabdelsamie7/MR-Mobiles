<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $brandId = $this->route('id') ?? $this->route('brand');
        return [
            'name' => 'required|string|max:255|unique:brands,name,' . $brandId,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4048',
        ];
    }
}
