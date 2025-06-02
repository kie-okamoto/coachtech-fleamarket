<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'in:credit_card,convenience_store'],
            'delivery_address' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => '支払い方法を選択してください。',
            'payment_method.in' => '有効な支払い方法を選択してください。',
            'delivery_address.required' => '配送先を選択してください。',
        ];
    }
}
