<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\TradeMessage;
use App\Models\TradeMessageRead; // 既読管理
use App\Models\TradeReview;      // 評価モデル
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// ★ 追加：メール送信用
use Illuminate\Support\Facades\Mail;
use App\Mail\TradeCompletedMail;

class TradeController extends Controller
{
    /**
     * 取引詳細（チャット）
     */
    public function show(Order $order)
    {
        $uid = Auth::id();

        // 関係者のみ（購入者 or 出品者）
        abort_unless(
            $uid && ($order->user_id === $uid || ($order->item && $order->item->user_id === $uid)),
            403
        );

        // 関連ロード（N+1防止）
        $order->load(['item.user', 'user', 'messages.user', 'tradeReviews']);

        // 会話は古い順
        $messages = $order->messages()->orderBy('created_at')->get();

        // ▼ 左サイド用：自分が関与している他の取引（現取引を除外）
        $me = Auth::user();

        if (method_exists($me, 'involvedOrders')) {
            $otherOrdersBase = $me->involvedOrders()
                ->where('orders.id', '!=', $order->id);
        } else {
            // 代替：involvedOrders() が無い場合
            $otherOrdersBase = Order::where(function ($q) use ($uid) {
                $q->where('user_id', $uid)
                    ->orWhereHas('item', fn($qq) => $qq->where('user_id', $uid));
            })->where('orders.id', '!=', $order->id);
        }

        // 並び順のエイリアスを last_message_at に固定
        $otherOrders = (clone $otherOrdersBase)
            ->with(['item.user'])
            ->withMax(['messages as last_message_at'], 'created_at')
            ->orderByRaw('COALESCE(last_message_at, orders.created_at) DESC')
            ->latest('orders.id') // タイ同率の安定ソート
            ->get();

        // 他取引の未読件数をまとめて算出（自分以外の投稿のみカウント）
        if ($otherOrders->isNotEmpty()) {
            $otherIds = $otherOrders->pluck('id');

            // order_id => 未読件数
            $unreadMap = TradeMessage::from('trade_messages as tm')
                ->select('tm.order_id', DB::raw('COUNT(*) as cnt'))
                ->whereIn('tm.order_id', $otherIds)
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

            $otherOrders->each(function ($o) use ($unreadMap) {
                $o->unread_count = (int) ($unreadMap[$o->id] ?? 0);
            });
        }

        // ▼ この取引を開いた時点で既読更新（自分の last_read_at を今に）
        TradeMessageRead::updateOrCreate(
            ['order_id' => $order->id, 'user_id' => $uid],
            ['last_read_at' => now()]
        );

        // ── 評価モーダル表示フラグ（購入者・出品者 共通） ──
        $meIsParticipant = in_array($uid, [
            $order->user_id,
            optional($order->item)->user_id,
        ], true);

        $computedShouldShow =
            !is_null($order->completed_at) &&           // 完了後
            $meIsParticipant &&                         // 参加者
            !$order->tradeReviews()->where('rater_id', $uid)->exists(); // 未レビュー

        // セッション（購入者が完了直後）に入っていたらそれも尊重
        $shouldShowReview = $computedShouldShow || (bool) session('show_review_modal', false);

        // 既存：完了ボタン表示用（購入者のみ）
        $isBuyer = ($uid === $order->user_id);

        return view('trades.show', [
            'order'             => $order,
            'messages'          => $messages,
            'otherOrders'       => $otherOrders,
            'isBuyer'           => $isBuyer,
            'show_review_modal' => $shouldShowReview,   // Blade はこのフラグだけを見る
        ]);
    }

    /**
     * メッセージ更新（本文のみ）
     */
    public function updateMessage(Request $request, Order $order, TradeMessage $message)
    {
        // この注文のメッセージか & 本人か
        abort_if($message->order_id !== $order->id, 404);
        abort_if($message->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:400'],
        ]);

        $message->update([
            'body' => $validated['body'],
        ]);

        return back()->with('status', 'メッセージを更新しました');
    }

    /**
     * メッセージ削除
     */
    public function destroyMessage(Request $request, Order $order, TradeMessage $message)
    {
        abort_if($message->order_id !== $order->id, 404);
        abort_if($message->user_id !== $request->user()->id, 403);

        if ($message->image_path) {
            Storage::disk('public')->delete($message->image_path);
        }

        $message->delete();

        return back()->with('status', 'メッセージを削除しました');
    }

    /**
     * 取引を完了（購入者のみ）。完了へ初めて遷移したタイミングで出品者に完了メールを送信。
     * 完了後にレビュー用モーダルを出す。
     */
    public function finish(Order $order)
    {
        $uid = Auth::id();

        // 購入者だけが完了できる
        abort_unless($order->user_id === $uid, 403);

        // まだ未完了 → 完了に更新し、その時だけ出品者へメール
        if (is_null($order->completed_at)) {
            $order->forceFill(['completed_at' => now()])->save();

            $order->loadMissing(['item.user', 'user']); // メール用に念のため
            $seller = optional($order->item)->user;

            if ($seller && $seller->email) {
                // MailHog に届きます（http://localhost:8025）
                Mail::to($seller->email)->send(new TradeCompletedMail($order));
            }
        }

        // モーダル表示用フラグをセッションに入れて詳細へ
        return redirect()
            ->route('trades.show', $order)
            ->with('show_review_modal', true);
    }

    /**
     * 評価を保存（購入者・出品者 共通）
     * 送信後はトップ（商品一覧）へリダイレクト。
     */
    public function storeReview(Request $request, Order $order)
    {
        $uid = Auth::id();

        // 参加者のみ
        $sellerId = optional($order->item)->user_id;
        $buyerId  = $order->user_id;
        abort_unless(in_array($uid, [$buyerId, $sellerId], true), 403);

        // 完了後のみ
        abort_if(is_null($order->completed_at), 409);

        // 二重投稿防止
        abort_if(
            TradeReview::where('order_id', $order->id)
                ->where('rater_id', $uid)
                ->exists(),
            409
        );

        // ★ コメントは廃止：スコアのみ受け取る
        $data = $request->validate([
            'score' => ['required', 'integer', 'between:1,5'],
        ]);

        // 相手（被評価者）を自動判定
        $revieweeId = ($uid === $buyerId) ? $sellerId : $buyerId;
        abort_if(!$revieweeId || $revieweeId === $uid, 403); // 自己評価禁止 + seller null 対策

        TradeReview::create([
            'order_id'      => $order->id,
            'rater_id'      => $uid,
            'rated_user_id' => $revieweeId,
            'score'         => (int) $data['score'],
        ]);

        // 評価後は商品一覧へ
        return redirect()->route('index')->with('status', '評価を送信しました。');
    }
}
