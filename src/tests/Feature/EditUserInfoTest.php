<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditUserInfoTest extends TestCase
{
    use RefreshDatabase;

    /** @test 編集ページにユーザー情報の初期値が表示される */
    public function test_edit_page_displays_user_info_initial_values()
    {
        // ユーザー＆住所を1件ずつ手動作成
        $user = User::create([
            'name' => '編集太郎',
            'email' => 'edit@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
            'profile_image' => null,
        ]);

        Address::create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都品川区1-2-3',
            'building' => '第2ビル301',
        ]);

        // 編集画面にアクセス（ミドルウェアを無効化）
        $response = $this->actingAs($user)
            ->withoutMiddleware([\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class])
            ->get('/mypage/profile');

        $response->assertStatus(200);

        // 初期値が表示されているか検証
        $response->assertSee('編集太郎');
        $response->assertSee('123-4567');
        $response->assertSee('東京都品川区1-2-3');
        $response->assertSee('第2ビル301');
    }
}
