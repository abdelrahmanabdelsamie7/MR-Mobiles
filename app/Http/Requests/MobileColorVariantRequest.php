<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class MobileColorVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'mobile_id' => 'required|exists:mobiles,id',
            'color_id' => 'required|exists:colors,id',
            'stock_quantity' => 'required|integer|min:0',
            'is_default' => 'nullable|boolean',
        ];
    }
}