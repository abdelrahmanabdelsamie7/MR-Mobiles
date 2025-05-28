<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class AccessoryColorVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'accessory_id' => 'required|exists:accessories,id',
            'color_id' => 'required|exists:colors,id',
            'stock_quantity' => 'required|integer|min:0',
        ];
    }
}
