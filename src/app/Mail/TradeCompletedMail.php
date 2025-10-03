<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TradeCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order; // ビューに渡す用

    public function __construct(Order $order)
    {
        // 必要な関連をロードしておく（N+1/ヌル対策）
        $this->order = $order->loadMissing(['item.user', 'user']);
    }

    public function build()
    {
        $item   = $this->order->item;
        $buyer  = $this->order->user;
        $seller = optional($item)->user;

        $itemName = $item->name ?? '商品';

        return $this->subject("【取引完了】{$itemName} が取引完了しました")
            ->markdown('emails.trades.completed', [
                'order'  => $this->order,
                'item'   => $item,
                'buyer'  => $buyer,
                'seller' => $seller,
            ]);
    }
}
