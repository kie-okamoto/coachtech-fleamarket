<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // id=1 の admin を登録 or 更新
        User::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::factory()->count(4)->create();
    }
}
