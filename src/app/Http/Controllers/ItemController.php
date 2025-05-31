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
            $user = Auth::user();

            if ($page === 'sell') {
                // 自分以外の商品（おすすめ）
                $query = Item::where('user_id', '!=', $user->id)
                    ->with(['order', 'categories']);
            } elseif ($page === 'buy') {
                // 自分がいいねした商品（マイリスト）
                $query = $user->favorites()->with(['order', 'categories']);
            } else {
                // デフォルト：全商品
                $query = Item::with(['order', 'categories']);
            }

            if ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }

            $products = $query->get();
        } else {
            // 未ログイン：全商品（mylistは除外）
            $query = Item::with(['order', 'categories']);

            if ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }

            $products = $query->get();
        }

        return view('items.index', compact('products', 'keyword'));
    }

    public function show($item_id)
    {
        $item = Item::with(['categories', 'comments.user', 'favoritedUsers'])->findOrFail($item_id);
        return view('items.show', compact('item'));
    }


    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ]);

        $imagePath = $request->file('image')->store('image', 'public');

        $item = new Item();
        $item->user_id = Auth::id();
        $item->image = $imagePath;
        $item->name = $request->name;
        $item->brand = $request->brand;
        $item->description = $request->description;
        $item->price = $request->price;
        $item->condition = $request->condition;
        $item->save();

        $item->categories()->sync($request->categories);

        return redirect()->route('mypage', ['page' => 'sell'])->with('success', '商品を出品しました');
    }
}
