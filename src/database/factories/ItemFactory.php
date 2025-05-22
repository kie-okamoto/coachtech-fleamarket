<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    public function definition(): array
    {
        // 事前に定義済みの商品情報をまとめた配列（名前・画像・説明・状態）
        $items = [
            [
                'name' => '腕時計',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'image/Armani+Mens+Clock.jpg',
                'condition' => '良好',
            ],
            [
                'name' => 'HDD',
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'image/HDD+Hard+Disk.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'name' => '玉ねぎ3束',
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'image/iLoveIMG+d.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'name' => '革靴',
                'description' => 'クラシックなデザインの革靴',
                'image' => 'image/Leather+Shoes+Product+Photo.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'name' => 'ノートPC',
                'description' => '高性能なノートパソコン',
                'image' => 'image/Living+Room+Laptop.jpg',
                'condition' => '良好',
            ],
            [
                'name' => 'マイク',
                'description' => '高音質のレコーディング用マイク',
                'image' => 'image/Music+Mic+4632231.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'name' => 'ショルダーバッグ',
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'image/Purse+fashion+pocket.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'name' => 'タンブラー',
                'description' => '使いやすいタンブラー',
                'image' => 'image/Tumbler+souvenir.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'name' => 'コーヒーミル',
                'description' => '手動のコーヒーミル',
                'image' => 'image/Waitress+with+Coffee+Grinder.jpg',
                'condition' => '良好',
            ],
            [
                'name' => 'メイクセット',
                'description' => '便利なメイクアップセット',
                'image' => 'image/外出メイクアップセット.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
        ];

        $item = $this->faker->randomElement($items);

        return [
            'name' => $item['name'],
            'description' => $item['description'],
            'image' => $item['image'],
            'condition' => $item['condition'],
            'price' => $this->faker->randomElement([500, 1200, 2500, 4000, 8000, 15000, 30000, 45000]),
            'user_id' => 1,
            'category_id' => $this->faker->numberBetween(1, 15),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
