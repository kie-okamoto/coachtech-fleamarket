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

  {{-- ユーザー情報セクション（幅1200px） --}}
  <div class="profile">
    <div class="profile__user">
      @if (Auth::user()->profile_image)
      <img src="{{ asset('storage/' . Auth::user()->profile_image) . '?v=' . time() }}"
        alt="プロフィール画像"
        class="profile__icon">
      @else
      <div class="profile__icon profile__icon--placeholder"></div>
      @endif

      <div class="profile__name">{{ Auth::user()->name }}</div>

      <a href="{{ route('profile.edit') }}" class="profile__edit-button">
        プロフィールを編集
      </a>
    </div>
  </div>

  {{-- タブ（全幅表示） --}}
  <div class="tabs-wrapper">
    <div class="tabs-container">
      <div class="tabs">
        <a href="{{ route('mypage', ['page' => 'sell']) }}" class="tab {{ request('page') !== 'buy' ? 'active' : '' }}">
          出品した商品
        </a>
        <a href="{{ route('mypage', ['page' => 'buy']) }}" class="tab {{ request('page') === 'buy' ? 'active' : '' }}">
          購入した商品
        </a>
      </div>
    </div>
  </div>

  {{-- 商品グリッド（全幅） --}}
  <div class="product-grid">
    @forelse ($items as $item)
    <div class="product-card">
      <div class="product-image">
        <a href="{{ route('items.show', $item->id) }}">
          <img src="{{ asset('storage/' . $item->image) }}"
            alt="{{ $item->name }}"
            onerror="this.src='{{ asset('images/no-image.png') }}'">
        </a>

        @if($item->is_sold ?? false)
        <div class="sold-overlay">SOLD</div>
        @endif
      </div>

      <p class="product-name">{{ $item->name }}</p>
    </div>
    @empty
    <div class="no-items-message">
      <p>商品がありません。</p>
    </div>
    @endforelse
  </div>

</body>

</html>