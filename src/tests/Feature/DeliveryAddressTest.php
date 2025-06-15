<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class DeliveryAddressTest extends TestCase
{
    use RefreshDatabase;

    /** @test 購入画面に配送先住所が反映されている */
    public function test_address_is_displayed_on_purchase_page()
    {
        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '111-2222',
            'address' => '東京都千代田区1-1-1',
            'building' => 'テストビル201',
        ]);

        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => bcrypt('password'),
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

        $response = $this->actingAs($user)->get("/purchase/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('東京都千代田区1-1-1'); // 住所表示確認
        $response->assertSee('テストビル201');      // 建物名表示確認
    }

    /** @test 購入時に正しく住所が紐づいて保存される */
    public function test_address_id_is_saved_in_order()
    {
        // Stripeセッションモック
        $mock = Mockery::mock('alias:Stripe\Checkout\Session');
        $mock->shouldReceive('retrieve')->with('dummy')->andReturn((object)['id' => 'dummy']);

        $user = User::create([
            'name' => '購入者',
            'email' => 'buyer@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都港区1-2-3',
            'building' => '新住所ビル101',
        ]);

        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品2',
            'image' => 'sample.jpg',
            'condition' => '新品',
            'description' => '商品説明',
            'price' => 5000,
        ]);

        $response = $this->actingAs($user)->get("/success?item_id={$item->id}&session_id=dummy");

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'user_id'    => $user->id,
            'item_id'    => $item->id,
            'address_id' => $address->id,
        ]);
    }
}
