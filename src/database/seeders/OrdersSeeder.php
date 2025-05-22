<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Item;

class OrdersSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        $users = User::pluck('id')->toArray();
        $items = Item::pluck('id')->toArray();

        foreach ($items as $itemId) {
            Order::create([
                'user_id' => $faker->randomElement($users),
                'item_id' => $itemId,
                'payment_method' => $faker->randomElement(['credit_card', 'bank_transfer', 'cash_on_delivery']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
