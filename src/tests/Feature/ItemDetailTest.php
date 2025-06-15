<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_detail_page_displays_correct_information()
    {
        // 出品者ユーザー作成
        $user = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => bcrypt('password123'),
        ]);

        // カテゴリを複数作成
        $category1 = Category::create(['name' => 'ファッション']);
        $category2 = Category::create(['name' => 'レディース']);

        // 商品作成
        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'レディースバッグ',
            'brand' => 'GUCCI',
            'image' => 'sample.jpg',
            'condition' => '新品',
            'description' => '高級レディースバッグです',
            'price' => 50000,
        ]);

        // 中間テーブルにカテゴリを紐づけ
        $item->categories()->attach([$category1->id, $category2->id]);

        // 商品詳細ページにアクセス
        $response = $this->get('/item/' . $item->id);

        // 情報が表示されているか確認
        $response->assertStatus(200);
        $response->assertSee('レディースバッグ');
        $response->assertSee('GUCCI');
        $response->assertSee('新品');
        $response->assertSee('高級レディースバッグです');
        $response->assertSee('50,000');
        $response->assertSee('ファッション');
        $response->assertSee('レディース');
    }
}
