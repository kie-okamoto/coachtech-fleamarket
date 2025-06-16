<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_example()
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        Item::create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'image' => 'sample.jpg',
            'condition' => '新品',
            'description' => 'テスト説明',
            'price' => 1000,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
