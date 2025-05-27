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

        $products = collect();

        if (Auth::check()) {
            if ($page === 'sell') {
                $query = Item::where('user_id', Auth::id());
            } elseif ($page === 'buy') {
                $query = Auth::user()->purchasedItems(); // ★Userモデルにリレーション定義が必要
            } else {
                $query = Item::where('user_id', '!=', Auth::id());
            }

            if ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }

            // ★ orderリレーションを事前に読み込む
            $products = $query->with('order')->get();
        } else {
            $query = Item::query();

            if ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }

            $products = $query->with('order')->get(); // ★未ログイン時も order を読み込む
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
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }
}
