<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page');
        $keyword = $request->query('keyword');

        // 商品リスト初期化
        $products = collect();

        if (Auth::check()) {
            if ($page === 'sell') {
                // 自分が出品した商品
                $query = Item::where('user_id', Auth::id());
            } elseif ($page === 'buy') {
                // 購入した商品（今後、ordersテーブルとリレーションする想定）
                $query = Auth::user()->purchasedItems(); // ★このリレーションはUserモデルに定義が必要
            } else {
                // ログインしているがページ指定なし → 通常商品（自分以外）
                $query = Item::where('user_id', '!=', Auth::id());
            }

            // 検索条件（ログイン時）
            if ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }

            $products = $query->get();
        } else {
            // 未ログイン：おすすめ（全商品）
            $query = Item::query();

            if ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }

            $products = $query->get();
        }

        return view('items.index', compact('products', 'keyword'));
    }

    public function show($item_id)
    {
        $item = \App\Models\Item::findOrFail($item_id);

        return view('items.show', compact('item'));
    }


    public function create()
    {
        $categories = Category::all(); // カテゴリ一覧を取得
        return view('items.create', compact('categories'));
    }
}
