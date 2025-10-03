@component('mail::message')
# 取引完了のお知らせ

{{ $seller->name ?? '出品者' }} 様

以下の商品が購入者によって「取引完了」とされました。

- 商品名: **{{ $item->name ?? '商品' }}**
- 価格: @if(isset($item->price)) ¥{{ number_format($item->price) }} @else 不明 @endif
- 購入者: {{ $buyer->name ?? '購入者' }}
- 取引ID: #{{ $order->id }}

@component('mail::button', ['url' => route('trades.show', $order)])
取引画面を開く
@endcomponent

ありがとうございます。
{{ config('app.name') }}
@endcomponent