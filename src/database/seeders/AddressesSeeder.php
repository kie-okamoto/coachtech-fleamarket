<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\User;

class AddressesSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        foreach (User::all() as $user) {
            Address::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'postal_code' => $faker->postcode(),
                    'address' => $faker->address(),
                    'building' => $faker->secondaryAddress(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
