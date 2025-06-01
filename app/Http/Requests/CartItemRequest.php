<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
class CartItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'product_id' => [
                'required',
                'uuid',
                function ($attribute, $value, $fail) {
                    if ($this->product_type === 'mobile' && !DB::table('mobiles')->where('id', $value)->exists()) {
                        $fail('The selected product_id is invalid for mobiles.');
                    }
                    if ($this->product_type === 'accessory' && !DB::table('accessories')->where('id', $value)->exists()) {
                        $fail('The selected product_id is invalid for accessories.');
                    }
                },
            ],
            'product_type' => ['required', Rule::in(['mobile', 'accessory'])],
            'product_color_id' => [
                'nullable',
                'uuid',
                function ($attribute, $value, $fail) {
                    if ($this->product_type === 'mobile') {
                        if (!$value || !DB::table('mobile_color_variants')->where('id', $value)->exists()) {
                            $fail('The product_color_id is invalid or missing for mobile.');
                        }
                    }
                    if ($this->product_type === 'accessory') {
                        if (!$value || !DB::table('accessory_color_variants')->where('id', $value)->exists()) {
                            $fail('The product_color_id is invalid or missing for accessory.');
                        }
                    }
                },
            ],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
    public function messages()
    {
        return [
            'product_id.required' => 'Product ID is required.',
            'product_id.uuid' => 'Product ID must be a valid UUID.',
            'product_type.required' => 'Product type is required.',
            'product_type.in' => 'Product type must be either mobile or accessory.',
            'product_color_id.exists' => 'Selected product color does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be an integer.',
            'quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}