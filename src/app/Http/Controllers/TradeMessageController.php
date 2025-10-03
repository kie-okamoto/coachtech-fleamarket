<?php

namespace App\Http\Controllers;

use App\Http\Requests\TradeMessageRequest;
use App\Models\Order;
use App\Models\TradeMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TradeMessageController extends Controller
{
    public function store(TradeMessageRequest $request, Order $order)
    {
        $uid = Auth::id();

        // 関係者（購入者/出品者）チェック
        $isInvolved = $order->user_id === $uid
            || ($order->item && $order->item->user_id === $uid);

        abort_unless($isInvolved, 403);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('trade_messages', 'public');
        }

        TradeMessage::create([
            'order_id'   => $order->id,
            'user_id'    => $uid,
            'body'       => $request->input('body'),
            'image_path' => $path,
        ]);

        return back()->with('success', 'メッセージを投稿しました');
    }
}
