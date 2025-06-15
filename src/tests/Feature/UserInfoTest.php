<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserInfoTest extends TestCase
{
    use RefreshDatabase;

    /** @test プロフィールページに出品情報が表示される */
    public function test_profile_page_displays_user_and_selling_items()
    {
        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
            'profile_image' => 'profile.png',
        ]);

        Item::create([
            'user_id' => $user->id,
            'name' => '出品商品A',
            'image' => 'item_a.jpg',
            'condition' => '新品',
            'description' => '説明文A',
            'price' => 3000,
        ]);

        $response = $this->actingAs($user)
            ->withoutMiddleware([\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class])
            ->get('/mypage'); // ← 初期タブは「出品商品」

        $response->assertStatus(200);
        $response->assertSee('テスト太郎');
        $response->assertSee('profile.png');
        $response->assertSee('出品商品A');
    }

    /** @test 購入商品がマイページの購入タブで表示される */
    public function test_profile_page_displays_purchased_items()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '100-0001',
            'address' => '東京都中央区1-1-1',
            'building' => 'ビル101',
        ]);

        $seller = User::factory()->create(['email_verified_at' => now()]);
        $purchasedItem = Item::create([
            'user_id' => $seller->id,
            'name' => '購入商品B',
            'image' => 'buy.jpg',
            'condition' => '中古',
            'description' => '説明文B',
            'price' => 2000,
        ]);

        Order::create([
            'user_id' => $user->id,
            'item_id' => $purchasedItem->id,
            'address_id' => $address->id,
            'payment_method' => 'card',
        ]);

        $response = $this->actingAs($user)
            ->get('/mypage?page=buy'); // ← 購入商品一覧を表示するタブ

        $response->assertStatus(200);
        $response->assertSee('購入商品B');
    }
}
