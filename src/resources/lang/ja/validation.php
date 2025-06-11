<?php

return [
  'required' => ':attribute は必須です。',
  'regex'    => ':attribute の形式が正しくありません。',
  'max'      => [
    'string' => ':attribute は :max 文字以内で入力してください。',
  ],
  'attributes' => [
    'postal_code' => '郵便番号',
    'address'     => '住所',
    'building'    => '建物名',
    'payment_method' => '支払い方法',
  ],
];
