<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test 部分一致で検索できる */
    public function test_partial_name_match_search_shows_correct_items()
    {
        $user = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => bcrypt('password123'),
        ]);

        Item::create([
            'user_id' => $user->id,
            'name' => 'レディースバッグ',
            'image' => 'sample1.jpg',
            'condition' => '新品',
            'description' => 'かわいいバッグです',
            'price' => 1000,
        ]);

        Item::create([
            'user_id' => $user->id,
            'name' => 'メンズ財布',
            'image' => 'sample2.jpg',
            'condition' => '未使用',
            'description' => 'スタイリッシュな財布',
            'price' => 2000,
        ]);

        $response = $this->get('/?keyword=バッグ');

        $response->assertStatus(200);
        $response->assertSee('レディースバッグ');
        $response->assertDontSee('メンズ財布');
    }


    /** @test 検索キーワードがマイリストでも保持されている */
    public function test_search_keyword_is_retained_on_mypage()
    {
        $user = User::create([
            'name' => 'KIE',
            'email' => 'kie@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user)->get('/?keyword=バッグ&page=buy');

        $response->assertStatus(200);
        $response->assertSee('バッグ');
    }
}
