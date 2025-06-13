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
        $faker = \Faker\Factory::create();
        $users = \App\Models\User::pluck('id')->toArray();
        $items = \App\Models\Item::pluck('id')->toArray();

        foreach ($items as $itemId) {
            \App\Models\Comment::factory()->create([
                'user_id' => $faker->randomElement($users),
                'item_id' => $itemId,
            ]);
        }
    }
}
