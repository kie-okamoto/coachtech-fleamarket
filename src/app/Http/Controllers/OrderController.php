<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Order;
use App\Http\Requests\PurchaseRequest;

class OrderController extends Controller
{
    public function purchase($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();
        $address = $user->address ?? null;

        return view('orders.purchase', compact('item', 'address'));
    }

    public function confirm(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        // 購入処理（例: Order 登録）
        Order::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => $request->payment_method,
        ]);

        return redirect('/')->with('message', '購入が完了しました！');
    }
}
