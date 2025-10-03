<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Order;
use App\Models\TradeMessage;
use App\Models\TradeMessageRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * マイページ（プロフィール表示）
     */
    public function profile(Request $request)
    {
        $user = Auth::user();
        $tab  = $request->query('tab', 'selling'); // selling | bought | trading

        // ── 出品した商品 ─────────────────────────────
        $sellingItems = $user->items()
            ->with('order')
            ->latest()
            ->paginate(12, ['*'], 'selling_page');

        // ── 購入した商品（orders 経由） ────────────────
        $boughtOrders = Order::with(['item.user'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(12, ['*'], 'bought_page');

        /**
         * ── 取引中（= ユーザーが関与する全注文） ─────────
         * - 購入者: orders.user_id = $user->id
         * - 出品者: orders.item.user_id = $user->id
         */
        $tradingOrdersBase = Order::where(function ($q) use ($user) {
            $q->where('user_id', $user->id) // 購入者として関与
                ->orWhereHas('item', fn($qq) => $qq->where('user_id', $user->id)); // 出品者として関与
        });

        // 表示用：最新メッセージ or 注文作成の新しい順（last_message_at を別名で持たせる）
        $tradingOrders = (clone $tradingOrdersBase)
            ->with(['item.user'])
            ->withMax(['messages as last_message_at'], 'created_at')
            ->orderByRaw('COALESCE(last_message_at, orders.created_at) DESC')
            ->latest('orders.id') // タイ時の安定ソート
            ->paginate(12, ['*'], 'trading_page');

        // 取引中の総件数（ページネーションの total）
        $tradingCount = $tradingOrders->total();

        /**
         * ── タブの赤バッジ用：合計未読メッセージ数（全注文） ──
         */
        $tradingOrderIds = (clone $tradingOrdersBase)->pluck('orders.id');

        if ($tradingOrderIds->isEmpty()) {
            $tradingMessageCount = 0;
        } else {
            $uid = $user->id;

            $perOrderUnreadAll = TradeMessage::from('trade_messages as tm')
                ->select('tm.order_id', DB::raw('COUNT(*) as cnt'))
                ->whereIn('tm.order_id', $tradingOrderIds)
                ->where('tm.user_id', '<>', $uid) // 自分の投稿は未読対象外
                ->leftJoin('trade_message_reads as r', function ($j) use ($uid) {
                    $j->on('r.order_id', '=', 'tm.order_id')
                        ->where('r.user_id', '=', $uid);
                })
                ->where(function ($q) {
                    $q->whereNull('r.last_read_at')
                        ->orWhereColumn('tm.created_at', '>', 'r.last_read_at');
                })
                ->groupBy('tm.order_id')
                ->pluck('cnt', 'tm.order_id'); // [order_id => 未読件数]

            $tradingMessageCount = $perOrderUnreadAll->sum();
        }

        /**
         * ── 一覧カードの左上バッジ用：このページに出ている注文だけ未読件数を付与 ──
         */
        if ($tradingOrders->count() > 0) {
            $uid = $user->id;
            $pageOrderIds = $tradingOrders->getCollection()->pluck('id');

            $perOrderUnreadThisPage = TradeMessage::from('trade_messages as tm')
                ->select('tm.order_id', DB::raw('COUNT(*) as cnt'))
                ->whereIn('tm.order_id', $pageOrderIds)
                ->where('tm.user_id', '<>', $uid)
                ->leftJoin('trade_message_reads as r', function ($j) use ($uid) {
                    $j->on('r.order_id', '=', 'tm.order_id')
                        ->where('r.user_id', '=', $uid);
                })
                ->where(function ($q) {
                    $q->whereNull('r.last_read_at')
                        ->orWhereColumn('tm.created_at', '>', 'r.last_read_at');
                })
                ->groupBy('tm.order_id')
                ->pluck('cnt', 'tm.order_id');

            // ページ上のコレクションに unread_count を付与
            $tradingOrders->setCollection(
                $tradingOrders->getCollection()->map(function ($o) use ($perOrderUnreadThisPage) {
                    $o->unread_count = (int)($perOrderUnreadThisPage[$o->id] ?? 0);
                    return $o;
                })
            );
        }

        /**
         * ── プロフィール用：平均評価 & 件数 ───────────────
         * 0件なら非表示にしやすいよう件数をそのまま渡す
         */
        $ratingStats = $user->receivedReviews()
            ->selectRaw('AVG(score) as avg_score, COUNT(*) as cnt')
            ->first();

        $ratingCount = (int) ($ratingStats->cnt ?? 0);
        $roundedAvg  = $ratingCount > 0 ? (int) round($ratingStats->avg_score) : null;

        return view('users.profile', compact(
            'user',
            'tab',
            'sellingItems',
            'boughtOrders',
            'tradingOrders',
            'tradingCount',
            'tradingMessageCount', // タブ合計未読
            'ratingCount',
            'roundedAvg'
        ));
    }

    /**
     * プロフィール編集画面の表示
     */
    public function editProfile()
    {
        $user = Auth::user();
        $address = $user->address;

        return view('users.edit_profile', compact('user', 'address'));
    }

    /**
     * プロフィール更新処理
     */
    public function updateProfile(ProfileRequest $request)
    {
        $user = Auth::user();

        $validated = $request->validated();

        // ユーザー名
        $user->name = $validated['name'];

        // プロフィール画像
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        $user->save();

        // 住所を更新または作成
        $user->address()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'postal_code' => $validated['postal_code'],
                'address'     => $validated['address'],
                'building'    => $validated['building'],
            ]
        );

        return redirect()->route('index')->with('success', 'プロフィールを更新しました');
    }
}
