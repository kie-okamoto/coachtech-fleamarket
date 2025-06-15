<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_favorite_an_item()
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'image' => 'sample.jpg',
            'condition' => '新品',
            'description' => '説明',
            'price' => 1000,
        ]);

        $response = $this->actingAs($user)->post("/items/{$item->id}/favorite");

        $response->assertStatus(200);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    public function test_user_can_unfavorite_an_item()
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'image' => 'sample.jpg',
            'condition' => '新品',
            'description' => '説明',
            'price' => 1000,
        ]);

        // 先にいいねする
        $user->favorites()->attach($item->id);

        // 解除
        $response = $this->actingAs($user)->delete("/items/{$item->id}/favorite");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    public function test_favorited_icon_has_active_class()
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'image' => 'sample.jpg',
            'condition' => '新品',
            'description' => '説明',
            'price' => 1000,
        ]);

        $user->favorites()->attach($item->id);

        $response = $this->actingAs($user)->get("/item/{$item->id}");

        // 修正：実際に使われているクラスに変更
        $response->assertSee('heart active');
    }
}
