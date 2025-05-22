<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FavoriteFactory extends Factory
{
    public function definition(): array
    {
        return [
            // user_id と item_id は Seeder 側で注入
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
