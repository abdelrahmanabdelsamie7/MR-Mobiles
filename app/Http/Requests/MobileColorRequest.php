<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class MobileColorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'mobile_id' => 'required|exists:mobiles,id',
            'color' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4048'
        ];
    }
}
