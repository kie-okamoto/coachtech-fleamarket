<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Address;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;
use Stripe\Checkout\Session;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    /** @test 支払い方法の選択肢が画面に表示される */
    public function test_payment_method_options_are_displayed()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        Address::create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都新宿区1-2-3',
            'building' => 'テストビル101',
        ]);

        $seller = User::factory()->create(['email_verified_at' => now()]);

        $item = Item::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品',
            'image' => 'test.jpg',
            'condition' => '新品',
            'description' => 'テスト商品説明',
            'price' => 2000,
        ]);

        $response = $this->actingAs($user)->get("/purchase/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('支払い方法');
        $response->assertSee('カード払い');
        $response->assertSee('コンビニ払い');
    }

    /** @test 支払い方法の選択後にStripeリダイレクトが行われる */
    public function test_selected_payment_method_redirects_to_stripe()
    {
        // Stripe モック
        $stripeMock = Mockery::mock('alias:' . Session::class);
        $stripeMock->shouldReceive('create')
            ->andReturn(new class {
                public string $id = 'dummy_session_id';
                public string $url = '/dummy-stripe-url';
            });

        $user = User::factory()->create(['email_verified_at' => now()]);

        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => '987-6543',
            'address' => '東京都渋谷区4-5-6',
            'building' => 'サンプルビル202',
        ]);

        $seller = User::factory()->create(['email_verified_at' => now()]);

        $item = Item::create([
            'user_id' => $seller->id,
            'name' => '確認用商品',
            'image' => 'confirm.jpg',
            'condition' => '新品',
            'description' => '確認用の説明',
            'price' => 5000,
        ]);

        $response = $this->actingAs($user)->post("/purchase/{$item->id}/confirm", [
            'payment_method' => 'konbini',
            'address_id' => $address->id,
        ]);

        $response->assertRedirect('/dummy-stripe-url');
    }
}
