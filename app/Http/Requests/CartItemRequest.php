<?php
namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
class CartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'cart_id' => 'uuid|exists:carts,id',
            'product_id' => [
                'required',
                'uuid',
                function ($attribute, $value, $fail) {
                    if ($this->product_type === 'mobile' && !\DB::table('mobiles')->where('id', $value)->exists()) {
                        $fail('The selected product_id is invalid for mobiles.');
                    }
                    if ($this->product_type === 'accessory' && !\DB::table('accessories')->where('id', $value)->exists()) {
                        $fail('The selected product_id is invalid for accessories.');
                    }
                },
            ],
            'product_type' => ['required', Rule::in(['mobile', 'accessory'])],
            'product_color_id' => [
                'nullable',
                'uuid',
                'exists:mobile_colors,id',
                function ($attribute, $value, $fail) {
                    if ($this->product_type === 'mobile') {
                        if (!$value) {
                            $fail('The product_color_id is required for mobiles.');
                        }
                    }
                },
            ],
            'quantity' => 'required|integer|min:1',
            'price' => 'numeric|min:0',
        ];
    }

}
