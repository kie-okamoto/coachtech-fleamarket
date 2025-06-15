<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_items_are_displayed_on_index_page()
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        Item::create(['user_id' => $user->id, 'name' => '商品A', 'image' => 'image/a.jpg', 'condition' => '新品', 'description' => '説明A', 'price' => 1000]);
        Item::create(['user_id' => $user->id, 'name' => '商品B', 'image' => 'image/b.jpg', 'condition' => '中古', 'description' => '説明B', 'price' => 2000]);
        Item::create(['user_id' => $user->id, 'name' => '商品C', 'image' => 'image/c.jpg', 'condition' => '新品', 'description' => '説明C', 'price' => 3000]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('商品A');
        $response->assertSee('商品B');
        $response->assertSee('商品C');
    }

    public function test_sold_label_is_displayed_for_purchased_items()
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'sold@example.com',
            'password' => bcrypt('password123'),
        ]);

        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都テスト市',
            'building' => 'テストビル',
        ]);

        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'Sold商品',
            'image' => 'image/sold.jpg',
            'condition' => 'やや傷あり',
            'description' => '説明',
            'price' => 999,
        ]);

        Order::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'address_id' => $address->id,
            'payment_method' => 'card',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    public function test_user_items_are_not_displayed_in_index()
    {
        $user = User::create([
            'name' => 'マイユーザー',
            'email' => 'my@example.com',
            'password' => bcrypt('password123'),
        ]);

        Item::create([
            'user_id' => $user->id,
            'name' => '自分の商品',
            'image' => 'image/mine.jpg',
            'condition' => '新品',
            'description' => '説明',
            'price' => 1000,
        ]);

        $otherUser = User::create([
            'name' => '他人ユーザー',
            'email' => 'other@example.com',
            'password' => bcrypt('password123'),
        ]);

        Item::create([
            'user_id' => $otherUser->id,
            'name' => '他人の商品',
            'image' => 'image/other.jpg',
            'condition' => '中古',
            'description' => '説明',
            'price' => 2000,
        ]);

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('自分の商品');
        $response->assertSee('他人の商品');
    }
}
