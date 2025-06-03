<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use App\Models\Address;
use App\Http\Requests\PurchaseRequest;

class OrderController extends Controller
{
    /**
     * 商品購入画面を表示
     */
    public function purchase($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user()->load('address');

        // 最新の住所を明示的に取得
        $address = optional($user->address)->fresh();

        return view('orders.purchase', compact('item', 'address'));
    }

    /**
     * 購入処理（支払い方法バリデーション含む）
     */
    public function confirm(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        // すでに購入されていたらリダイレクト
        if ($item->order) {
            return redirect('/')
                ->with('error', 'この商品はすでに購入されています。');
        }

        // 購入処理
        Order::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => $request->payment_method,
        ]);

        return redirect('/')
            ->with('message', '購入が完了しました！');
    }


    /**
     * 配送先住所編集画面を表示
     */
    public function editAddress($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user()->load('address');
        $address = $user->address;

        return view('orders.edit_address', compact('item', 'address'));
    }

    /**
     * 配送先住所の更新処理
     */
    public function updateAddress(Request $request, $item_id)
    {
        $request->validate([
            'postal_code' => 'required|string|max:8',
            'address'     => 'required|string|max:255',
            'building'    => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        $user->address()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'postal_code' => $request->postal_code,
                'address'     => $request->address,
                'building'    => $request->building,
            ]
        );

        return redirect()->route('purchase', ['item_id' => $item_id])
            ->with('message', '配送先を更新しました。');
    }
}
