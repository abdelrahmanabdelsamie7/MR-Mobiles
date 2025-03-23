<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
           'name' => 'required|string|between:2,100',
           'email' => 'required|string|email|max:100',
           'message'=>'required|string|min:5|max:255',
        ];
    }
}