<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Http\Request;

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
                $query = Item::where('user_id', '!=', $user->id)->with(['order', 'categories']);
            } elseif ($page === 'buy') {
                $query = $user->favorites()->with(['order', 'categories']);
            } else {
                $query = Item::with(['order', 'categories']);
            }

            if ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }

            $products = $query->get();
        } else {
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

    // 修正：ExhibitionRequest に差し替え
    public function store(ExhibitionRequest $request)
    {
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
