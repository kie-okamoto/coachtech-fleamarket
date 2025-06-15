<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_user_can_post_comment()
    {
        $user = User::create([
            'name' => 'コメントユーザー',
            'email' => 'comment@example.com',
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

        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'comment' => 'これはテストコメントです',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'これはテストコメントです',
        ]);
    }

    public function test_guest_cannot_post_comment()
    {
        // userを作っておく（※ログインはしない）
        $user = User::create([
            'name' => 'ダミーユーザー',
            'email' => 'dummy@example.com',
            'password' => bcrypt('password123'),
        ]);

        $item = Item::create([
            'user_id' => $user->id, // ← ✅ 実在ユーザーIDを使う
            'name' => '未ログインテスト商品',
            'image' => 'sample.jpg',
            'condition' => '新品',
            'description' => '説明',
            'price' => 1000,
        ]);

        $response = $this->post("/item/{$item->id}/comment", [
            'comment' => 'ゲストコメント',
        ]);

        $response->assertRedirect('/login'); // Fortifyのリダイレクト確認
    }

    public function test_comment_is_required()
    {
        $user = User::create([
            'name' => 'KIE',
            'email' => 'kie@example.com',
            'password' => bcrypt('password123'),
        ]);

        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'バリデーションテスト商品',
            'image' => 'sample.jpg',
            'condition' => '新品',
            'description' => '説明',
            'price' => 1000,
        ]);

        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'comment' => '', // 空欄
        ]);

        $response->assertSessionHasErrors('comment');
    }

    public function test_comment_must_be_less_than_255_characters()
    {
        $user = User::create([
            'name' => 'KIE',
            'email' => 'kie@example.com',
            'password' => bcrypt('password123'),
        ]);

        $item = Item::create([
            'user_id' => $user->id,
            'name' => '長文テスト商品',
            'image' => 'sample.jpg',
            'condition' => '新品',
            'description' => '説明',
            'price' => 1000,
        ]);

        $longComment = str_repeat('あ', 256);

        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'comment' => $longComment,
        ]);

        $response->assertSessionHasErrors('comment');
    }
}
