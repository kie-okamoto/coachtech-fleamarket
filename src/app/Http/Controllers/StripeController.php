<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Order;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $item = Item::findOrFail($request->item_id);

        // 購入対象の商品をセッションに一時保存
        session(['purchased_item_id' => $item->id]);

        $session = Session::create([
            'payment_method_types' => ['card', 'konbini'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => (int)$item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.success'),
            'cancel_url' => route('stripe.cancel'),
        ]);

        return redirect($session->url);
    }

    public function success()
    {
        $itemId = session('purchased_item_id');

        if ($itemId && Auth::check()) {
            $item = Item::find($itemId);

            if ($item && !$item->order) {
                Order::create([
                    'user_id' => Auth::id(),
                    'item_id' => $item->id,
                    'payment_method' => 'stripe',
                ]);
            }

            session()->forget('purchased_item_id');
        }

        // 購入成功後のリダイレクト
        return redirect()->route('orders.success');
    }


    public function cancel()
    {
        return redirect('/')->with('error', '決済がキャンセルされました。');
    }
}
