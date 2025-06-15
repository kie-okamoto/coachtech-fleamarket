<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavoriteListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function favorited_items_are_displayed()
    {
        // ログインユーザーと出品ユーザーを作成
        $user = User::create([
            'name' => 'User1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
        ]);

        $seller = User::create([
            'name' => 'Seller',
            'email' => 'seller@example.com',
            'password' => bcrypt('password'),
        ]);

        // 出品ユーザーの商品を作成
        $item = Item::create([
            'user_id' => $seller->id,
            'name' => 'いいね商品',
            'image' => 'image.jpg',
            'condition' => '新品',
            'description' => '説明文',
            'price' => 1000,
        ]);

        // ログインユーザーが商品をお気に入り登録
        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // マイリストページにアクセス
        $response = $this->actingAs($user)->get('/?page=buy');

        $response->assertStatus(200);
        $response->assertSee('いいね商品');
    }

    /** @test */
    public function sold_label_is_displayed_in_favorites()
    {
        $user = User::create([
            'name' => 'Buyer',
            'email' => 'buyer@example.com',
            'password' => bcrypt('password'),
        ]);

        $seller = User::create([
            'name' => 'Seller',
            'email' => 'seller2@example.com',
            'password' => bcrypt('password'),
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'name' => '売れた商品',
            'image' => 'sold.jpg',
            'condition' => '新品',
            'description' => '説明',
            'price' => 2000,
        ]);

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区',
            'building' => '渋谷ビル101',
        ]);

        Order::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 'card',
        ]);

        $response = $this->actingAs($user)->get('/?page=buy');
        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /** @test */
    public function own_items_are_not_displayed_in_favorites()
    {
        $user = User::create([
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
        ]);

        $item = Item::create([
            'user_id' => $user->id,
            'name' => '自分の商品',
            'image' => 'self.jpg',
            'condition' => '未使用',
            'description' => 'マイアイテム',
            'price' => 3000,
        ]);

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get('/?page=buy');
        $response->assertStatus(200);
        $response->assertDontSee('自分の商品');
    }

    /** @test */
    public function guest_sees_nothing_on_favorites_page()
    {
        $response = $this->get('/?page=buy');
        $response->assertStatus(200);
        $response->assertSee('表示できる商品がありません'); // index.blade.phpに合わせて文言を調整
    }
}
