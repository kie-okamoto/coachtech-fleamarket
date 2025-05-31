<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $item->name }}｜商品詳細</title>

  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/show.css') }}">

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="{{ asset('js/favorite.js') }}" defer></script>
</head>

<body>
  @include('components.header')

  <main class="product-detail">
    <div class="product-detail__image">
      {{-- SOLD表示 --}}
      @if ($item->is_sold)
      <div class="sold-overlay">SOLD</div>
      @endif

      @php use Illuminate\Support\Str; @endphp
      <img src="{{ Str::startsWith($item->image, 'http') ? $item->image : asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
    </div>

    <div class="product-detail__info">
      <h1>{{ $item->name }}</h1>

      <p class="brand">ブランド名：{{ $item->brand ?? 'なし' }}</p>

      <p class="price">
        <span class="price-symbol">¥</span>{{ number_format($item->price) }}
        <span class="tax-label">（税込）</span>
      </p>

      <div class="icons">
        <button
          class="favorite-button"
          data-auth="{{ Auth::check() ? 'true' : 'false' }}"
          data-item-id="{{ $item->id }}"
          data-favorited="{{ Auth::check() && Auth::user()->favorites->contains($item->id) ? 'true' : 'false' }}">
          <span class="heart {{ Auth::check() && Auth::user()->favorites->contains($item->id) ? 'active' : '' }}">
            {{ Auth::check() && Auth::user()->favorites->contains($item->id) ? '★' : '☆' }}
          </span>
          <span class="favorite-count">{{ $item->favoritedUsers->count() }}</span>
        </button>

        <span class="comment-icon">💬</span>
        <span class="comment-count">{{ $item->comments->count() }}</span>
      </div>

      {{-- 購入ボタン --}}
      @auth
      @if ($item->is_sold)
      <button class="purchase-button disabled" disabled>売り切れました</button>
      @else
      <a href="{{ route('purchase', $item->id) }}" class="purchase-button">購入手続きへ</a>
      @endif
      @else
      <a href="{{ route('login') }}" class="purchase-button">購入手続きへ</a>
      @endauth

      <h2>商品説明</h2>
      @if (!empty($item->color))
      <p>カラー：{{ $item->color }}</p>
      @endif
      <p>{{ $item->description }}</p>

      <h2>商品の情報</h2>
      <p>カテゴリ：
        @forelse ($item->categories as $category)
        <span class="tag">{{ $category->name }}</span>
        @empty
        未設定
        @endforelse
      </p>
      <p>商品の状態：{{ $item->condition }}</p>

      <h2>コメント ({{ $item->comments->count() }})</h2>
      @foreach ($item->comments as $comment)
      <div class="comment">
        <div class="comment__header">
          <div class="comment__icon">
            @if ($comment->user && $comment->user->profile_image)
            <img src="{{ asset('storage/' . $comment->user->profile_image) }}" alt="プロフィール画像">
            @else
            <img src="{{ asset('images/no-image.png') }}" alt="ダミー画像">
            @endif
          </div>
          <div class="comment__user">{{ $comment->user->name ?? 'ゲスト' }}</div>
        </div>
        <div class="comment__body-wrapper">
          <div class="comment__body">{{ $comment->comment }}</div>
        </div>
      </div>
      @endforeach

      {{-- コメント投稿欄 --}}
      @auth
      <form action="{{ route('comment.store', $item->id) }}" method="POST">
        @csrf
        <textarea name="comment" class="comment-textarea" placeholder="商品のコメントを入力してください。">{{ old('comment') }}</textarea>
        @error('comment')
        <p class="error" style="color:red;">{{ $message }}</p>
        @enderror
        <button type="submit" class="comment-submit-button">コメント送信</button>
      </form>
      @else
      <form method="GET" action="{{ route('login') }}">
        <textarea class="comment-textarea" placeholder="ログインしてコメントを入力してください" disabled></textarea>
        <button type="submit" class="comment-submit-button">コメントを送信する</button>
      </form>
      @endauth
    </div>
  </main>
</body>

</html>