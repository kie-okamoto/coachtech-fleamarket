<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>マイページ</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
</head>

<body>
  @include('components.header')

  {{-- ユーザー情報 --}}
  <div class="profile">
    <div class="profile__user">
      @if ($user->profile_image)
      <img src="{{ asset('storage/' . $user->profile_image) . '?v=' . time() }}" alt="プロフィール画像" class="profile__icon">
      @else
      <div class="profile__icon profile__icon--placeholder"></div>
      @endif

      {{-- ユーザー名＋星（縦積み） --}}
      <div class="profile__info" style="flex:1 1 auto; min-width:240px;">
        <div class="profile__name">{{ $user->name }}</div>

        @if (($ratingCount ?? 0) > 0)
        <div class="rating-stars" aria-label="平均評価 {{ $roundedAvg }} / 5（{{ $ratingCount }}件）">
          @for ($i = 1; $i <= 5; $i++)
            <span class="star {{ $i <= (int) $roundedAvg ? 'is-on' : '' }}"></span>
            @endfor
        </div>
        @endif
      </div>

      <a href="{{ route('profile.edit') }}" class="profile__edit-button">プロフィールを編集</a>
    </div>
  </div>

  {{-- タブ --}}
  @php $activeTab = $tab ?? request('tab', 'selling'); @endphp
  <div class="tabs-wrapper">
    <div class="tabs-container">
      <div class="tabs">
        <a href="{{ route('mypage', ['tab' => 'selling']) }}" class="tab {{ $activeTab === 'selling' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage', ['tab' => 'bought'])  }}" class="tab {{ $activeTab === 'bought'  ? 'active' : '' }}">購入した商品</a>
        <a href="{{ route('mypage', ['tab' => 'trading']) }}" class="tab {{ $activeTab === 'trading' ? 'active' : '' }}">
          取引中の商品
          @if (($tradingMessageCount ?? 0) > 0)
          <span class="tab-badge">{{ $tradingMessageCount }}</span>
          @endif
        </a>
      </div>
    </div>
  </div>

  {{-- コンテンツ切替 --}}
  @if ($activeTab === 'selling')

  {{-- 出品した商品（空時メッセージをグリッド外で） --}}
  @if ($sellingItems->total() === 0)
  <div class="no-items-message">
    <p>商品がありません。</p>
  </div>
  @else
  <div class="product-grid">
    @foreach ($sellingItems as $item)
    <div class="product-card">
      <div class="product-image">
        <a href="{{ route('items.show', $item->id) }}">
          <img src="{{ asset('storage/' . $item->image) }}"
            alt="{{ $item->name }}"
            onerror="this.src='{{ asset('images/no-image.png') }}'">
        </a>
        @if ($item->is_sold ?? false)
        <div class="sold-overlay">SOLD</div>
        @endif
      </div>
      <p class="product-name">{{ $item->name }}</p>
    </div>
    @endforeach
  </div>
  {{ $sellingItems->withQueryString()->links() }}
  @endif

  @elseif ($activeTab === 'bought')

  {{-- 購入した商品 --}}
  @if ($boughtOrders->total() === 0)
  <div class="no-items-message">
    <p>商品がありません。</p>
  </div>
  @else
  <div class="mypage-orders">
    @include('users.partials.grid-orders', ['orders' => $boughtOrders])
  </div>
  {{ $boughtOrders->withQueryString()->links() }}
  @endif

  @else

  {{-- 取引中の商品 --}}
  @if ($tradingOrders->total() === 0)
  <div class="no-items-message">
    <p>商品がありません。</p>
  </div>
  @else
  <div class="product-grid">
    @foreach ($tradingOrders as $order)
    @php
    $item = $order->item;
    $thumb = $item && $item->image ? asset('storage/'.$item->image) : asset('images/no-image.png');
    @endphp
    <div class="product-card">
      <a href="{{ route('trades.show', $order) }}">
        <div class="product-image">
          <img src="{{ $thumb }}"
            alt="{{ $item ? $item->name : '商品' }}"
            onerror="this.src='{{ asset('images/no-image.png') }}'">
          @if(($order->unread_count ?? 0) > 0)
          <span class="badge-unread">{{ $order->unread_count }}</span>
          @endif
        </div>
      </a>
      <p class="product-name">{{ $item ? $item->name : '商品' }}</p>
    </div>
    @endforeach
  </div>
  {{ $tradingOrders->withQueryString()->links() }}
  @endif

  @endif
</body>

</html>