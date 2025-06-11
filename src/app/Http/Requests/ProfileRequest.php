<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'postal_code'  => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address'      => ['required', 'string', 'max:255'],
            'building'     => ['required', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ユーザー名は必須です。',
            'postal_code.required' => '郵便番号は必須です。',
            'postal_code.regex' => '郵便番号はハイフンありの8桁形式（例：123-4567）で入力してください。',
            'address.required' => '住所は必須です。',
            'building.required' => '建物名は必須です。',
            'profile_image.image' => '画像ファイルを選択してください。',
            'profile_image.mimes' => 'jpegまたはpng形式のファイルを選択してください。',
        ];
    }
}
