<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;

class ItemsSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('id', 1)->exists()) {
            User::factory()->create([
                'id' => 1,
                'name' => 'admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // カテゴリ
        $categoryNames = [
            'ファッション',
            '家電',
            'インテリア',
            'レディース',
            'メンズ',
            'コスメ',
            '本',
            'ゲーム',
            'スポーツ',
            'キッチン',
            'ハンドメイド',
            'アクセサリー',
            'おもちゃ',
            'ベビー・キッズ',
        ];

        foreach ($categoryNames as $name) {
            Category::firstOrCreate(['name' => $name]);
        }

        $categoryIds = Category::pluck('id')->all();

        // 商品データ（10件）
        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'image/Armani+Mens+Clock.jpg',
                'condition' => '良好',
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'image/HDD+Hard+Disk.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'image/iLoveIMG+d.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image' => 'image/Leather+Shoes+Product+Photo.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image' => 'image/Living+Room+Laptop.jpg',
                'condition' => '良好',
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image' => 'image/Music+Mic+4632231.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'image/Purse+fashion+pocket.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image' => 'image/Tumbler+souvenir.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image' => 'image/Waitress+with+Coffee+Grinder.jpg',
                'condition' => '良好',
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image' => 'image/外出メイクアップセット.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
        ];

        foreach ($items as $data) {
            $item = Item::create([
                'user_id' => 1,
                'name' => $data['name'],
                'price' => $data['price'],
                'description' => $data['description'],
                'image' => $data['image'],
                'condition' => $data['condition'],
            ]);

            // 1〜2カテゴリをランダムで紐付け（複数可）
            $randomCategoryIds = collect($categoryIds)->random(rand(1, 2))->all();
            $item->categories()->attach($randomCategoryIds);
        }
    }
}
