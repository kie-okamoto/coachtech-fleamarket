<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>商品一覧</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/index.css') }}">
</head>

<body>
  @include('components.header')

  <main class="main">
    @php
    $query = request()->except('page');
    @endphp

    <div class="tabs-wrapper">
      <div class="tabs-container">
        <div class="tabs">
          @auth
          <a href="{{ route('index', array_merge(['page' => 'sell'], $query)) }}" class="tab {{ request('page') === 'sell' ? 'active' : '' }}">おすすめ</a>
          <a href="{{ route('index', array_merge(['page' => 'buy'], $query)) }}" class="tab {{ request('page') === 'buy' ? 'active' : '' }}">マイリスト</a>
          @else
          <a href="{{ route('index', array_merge(['page' => 'new'], $query)) }}" class="tab {{ request('page') !== 'mylist' ? 'active' : '' }}">おすすめ</a>
          <a href="{{ route('index', array_merge(['page' => 'mylist'], $query)) }}" class="tab {{ request('page') === 'mylist' ? 'active' : '' }}">マイリスト</a>
          @endauth
        </div>
      </div>
    </div>

    <div class="product-grid">
      @if (Auth::check() || request('page') !== 'mylist')
      @forelse ($products as $product)
      <div class="product-card">
        <div class="product-image">
          <a href="{{ route('items.show', ['item_id' => $product->id]) }}">
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
          </a>
          @if(isset($product->is_sold) && $product->is_sold)
          <div class="sold-overlay">Sold</div>
          @endif
        </div>
        <p class="product-name">{{ $product->name }}</p>
      </div>
      @empty
      <p>表示できる商品がありません。</p>
      @endforelse
      @auth
      {{-- マイリストの表示（必要な場合ここに入れる） --}}
      @endauth
      @endif
    </div>
  </main>
</body>

</html>