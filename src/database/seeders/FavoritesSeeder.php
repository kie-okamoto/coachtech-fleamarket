<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Favorite;
use App\Models\User;
use App\Models\Item;

class FavoritesSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        $users = \App\Models\User::pluck('id')->toArray();
        $items = \App\Models\Item::pluck('id')->toArray();

        foreach ($users as $userId) {
            $favItems = collect($items)->random(min(2, count($items)));

            foreach ($favItems as $itemId) {
                \App\Models\Favorite::factory()->create([
                    'user_id' => $userId,
                    'item_id' => $itemId,
                ]);
            }
        }
    }
}
