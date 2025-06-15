<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test 出品商品情報が正しく保存される */
    public function test_item_can_be_created_with_valid_input()
    {
        // ダミー画像保存用のストレージ設定
        Storage::fake('public');

        // ユーザー作成
        $user = User::create([
            'name' => '出品者太郎',
            'email' => 'seller@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        // カテゴリ作成
        $category = Category::create([
            'name' => '家電',
        ]);

        // ダミー画像作成
        $file = UploadedFile::fake()->create('sample.jpg', 100, 'image/jpeg');

        // 認証付きでPOST送信
        $this->withoutMiddleware([\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class]);

        $response = $this->actingAs($user)->post('/sell', [
            'name' => '冷蔵庫2024',
            'image' => $file,
            'condition' => '中古',
            'description' => '美品の冷蔵庫です',
            'price' => 12000,
            'categories' => [$category->id],
        ]);

        // バリデーションエラーがないことを確認
        $response->assertSessionHasNoErrors();

        // リダイレクト確認
        $response->assertRedirect('/mypage?page=sell');


        // DBに保存確認（nameだけで十分）
        $this->assertDatabaseHas('items', [
            'name' => '冷蔵庫2024',
        ]);
    }
}
