<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // ここを DB に入れる相対パスに統一
        $dummy = 'profile_images/dummy.png';

        // ── 管理者（固定: id=1） ─────────────────────────────
        User::updateOrCreate(
            ['id' => 1],
            [
                'name'              => 'admin',
                'email'             => 'admin@example.com',
                'password'          => Hash::make('admin1234'),
                'email_verified_at' => now(),
                'profile_image'     => $dummy,
            ]
        );

        // 出品者A
        User::updateOrCreate(
            ['email' => 'user_a@example.com'],
            [
                'name'              => 'User A',
                'password'          => Hash::make('aaaa1234'),
                'email_verified_at' => now(),
                'profile_image'     => $dummy,
            ]
        );

        // 出品者B
        User::updateOrCreate(
            ['email' => 'user_b@example.com'],
            [
                'name'              => 'User B',
                'password'          => Hash::make('bbbb1234'),
                'email_verified_at' => now(),
                'profile_image'     => $dummy,
            ]
        );

        // 出品なしユーザー
        User::updateOrCreate(
            ['email' => 'user_c@example.com'],
            [
                'name'              => 'User C',
                'password'          => Hash::make('cccc1234'),
                'email_verified_at' => now(),
                'profile_image'     => $dummy,
            ]
        );
    }
}
