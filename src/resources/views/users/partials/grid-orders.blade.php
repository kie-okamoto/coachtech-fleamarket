<div class="card-grid">
  @forelse ($orders as $order)
  @php $item = $order->item; @endphp

  <a href="{{ route('trades.show', $order) }}" class="card text-decoration-none">
    @if (!empty($showTradingBadge))
    <span class="notify-dot" aria-hidden="true"></span>
    @endif

    @if ($item)
    <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}" class="card__img">
    <div class="card__body">
      <div class="card__title">{{ $item->name }}</div>
    </div>
    @else
    <div class="card__body text-muted">商品情報が見つかりません</div>
    @endif
  </a>
  @empty
  <p class="text-muted">取引中の商品はありません。</p>
  @endforelse
</div>