<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\App;

class PurchaseItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test 商品を購入できる（直接 success を叩く） */
    public function test_user_can_purchase_an_item()
    {
        // Stripe\Sessionをモックとして差し替える
        $mock = Mockery::mock('alias:Stripe\Checkout\Session');
        $mock->shouldReceive('retrieve')
            ->with('dummy')
            ->andReturn((object)['id' => 'dummy']);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都港区1-2-3',
            'building' => 'テストビル101',
        ]);

        $seller = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品',
            'image' => 'sample.jpg',
            'condition' => '新品',
            'description' => 'テスト商品の説明',
            'price' => 3000,
        ]);

        $response = $this->actingAs($user)->get("/success?item_id={$item->id}&session_id=dummy");

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'card',
        ]);
    }

    /** @test 購入済み商品は「Sold」と表示される */
    public function test_sold_label_is_displayed_on_index()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都中央区1-2-3',
            'building' => 'テストビル202',
        ]);

        $item = Item::create([
            'user_id' => $user->id,
            'name' => '購入済み商品',
            'image' => 'sold.jpg',
            'condition' => '新品',
            'description' => '説明',
            'price' => 5000,
        ]);

        Order::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 'card',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /** @test 購入商品がプロフィールの購入一覧に表示される */
    public function test_purchased_item_is_shown_in_profile()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-2-3',
            'building' => 'テストビル303',
        ]);

        $seller = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'name' => 'プロフィール商品',
            'image' => 'mypage.jpg',
            'condition' => '新品',
            'description' => '説明',
            'price' => 8000,
        ]);

        Order::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 'card',
        ]);

        $response = $this->actingAs($user)->get('/mypage?page=buy');
        $response->assertStatus(200);
        $response->assertSee('プロフィール商品');
    }
}
