<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class ColorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $colorId = $this->route('id') ?? $this->route('color');
        return [
            'name' => 'required|string|max:255|unique:colors,name,' . $colorId,
            'hex_code' => 'required|string|max:255'
        ];
    }
}