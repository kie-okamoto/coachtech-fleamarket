<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\User;
use App\Models\Item;

class CommentsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create(); // Fakerを使って
        $users = \App\Models\User::pluck('id')->toArray(); // ユーザーID一覧
        $items = \App\Models\Item::pluck('id')->toArray(); // 商品ID一覧

        foreach ($items as $itemId) {
            \App\Models\Comment::factory()->create([
                'user_id' => $faker->randomElement($users), // ランダムなユーザーID
                'item_id' => $itemId,                        // 商品ID
            ]);
        }
    }
}
