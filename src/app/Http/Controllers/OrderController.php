<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use App\Http\Requests\PurchaseRequest;
use Stripe\Stripe;
use Stripe\Checkout\Session;

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

        // 購入済みかチェック
        if ($item->order) {
            return redirect('/')
                ->with('error', 'この商品はすでに購入されています。');
        }

        $validated = $request->validated();

        // ✅ Stripe初期化（正しい設定）
        Stripe::setApiKey(config('services.stripe.secret'));

        // ✅ Checkoutセッション作成
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $item->price,
                    'product_data' => [
                        'name' => $item->name,
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'customer_email' => $user->email,
            'success_url' => route('orders.success') . '?item_id=' . $item->id . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('purchase', ['item_id' => $item_id]),
        ]);

        // ✅ 決済ページにリダイレクト
        return redirect($session->url);
    }


    /**
     * 購入完了ページを表示
     */
    public function success(Request $request)
    {
        $user = Auth::user();

        $session_id = $request->query('session_id');
        $item_id = $request->query('item_id');

        if (!$session_id || !$item_id) {
            return redirect('/')->with('error', '決済情報が不足しています。');
        }

        // ✅ Stripe初期化（ここが必須！）
        Stripe::setApiKey(config('services.stripe.secret'));

        // ✅ Stripeからセッション情報を取得
        $session = Session::retrieve($session_id);

        $item = Item::findOrFail($item_id);

        if ($item->order) {
            return redirect('/')->with('error', 'すでに購入されています。');
        }

        $address = $user->address;
        if (!$address) {
            return redirect('/')->with('error', '配送先住所が登録されていません。');
        }

        // 注文保存
        Order::create([
            'user_id'        => $user->id,
            'item_id'        => $item->id,
            'address_id'     => $address->id,
            'payment_method' => 'card',
        ]);

        return view('orders.success');
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
    public function updateAddress(AddressRequest $request, $item_id)
    {
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
