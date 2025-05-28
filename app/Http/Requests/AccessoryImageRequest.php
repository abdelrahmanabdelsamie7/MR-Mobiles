<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class AccessoryImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'accessory_color_variant_id' => 'required|exists:accessory_color_variants,id',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:4048',
        ];
    }
}
