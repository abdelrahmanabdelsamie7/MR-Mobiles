<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class WishlistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
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
        ];
    }
}
