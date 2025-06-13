<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            //'name' => ['required', 'string'],
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address'     => ['required', 'string'],
            'building'    => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            //'name.required' => 'お名前は必須です。',
            'postal_code.required' => '郵便番号は必須です。',
            'postal_code.regex' => '郵便番号はハイフンありの8桁形式（例：123-4567）で入力してください。',
            'address.required' => '住所は必須です。',
            'building.required' => '建物名は必須です。',
        ];
    }
}
