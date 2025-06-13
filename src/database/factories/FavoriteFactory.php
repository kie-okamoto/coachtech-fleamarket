<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FavoriteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
