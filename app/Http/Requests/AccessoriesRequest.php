<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class AccessoriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $accessoryId = $this->route('id') ?? $this->route('accessory');;
         return [
            'title'          => 'required|string|max:255|unique:accessories,title,' . $accessoryId ,
            'brand_id'       => 'required|exists:brands,id',
            'description'    => 'nullable|string',
            'battery'        => 'nullable|integer|min:100',
            'speed'        => 'nullable',
            'color'          => 'nullable|string|max:50',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4048',
            'price'          => 'required|numeric|min:0',
            'discount'       => 'nullable|integer|min:0|max:100',
            'stock_quantity' => 'nullable|integer|min:0',
            'status'         => 'required|in:available,out_of_stock,coming_soon',
        ];
    }
}