<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class MobileImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
           'mobile_id' => 'required|exists:mobiles,id',
           'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4048'
        ];
    }
}
