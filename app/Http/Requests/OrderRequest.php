<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => 'required|in:instapay,vodafone_cash,cod',
            'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:4048',
            'note' => 'nullable|string|max:1000',
            'order_items' => 'required|array|min:1',
            'order_items.*.product_id' => 'required|uuid',
            'order_items.*.product_type' => 'required|in:mobile,accessory',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.price' => 'required|numeric|min:0',
            'order_items.*.product_color_id' => 'nullable|uuid|exists:mobile_color_variants,id',
        ];
    }
}