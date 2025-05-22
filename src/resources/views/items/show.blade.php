<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $item->name }}｜商品詳細</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/show.css') }}">
</head>

<body>
  @include('components.header')


  <main class="product-detail">
    <div class="product-detail__image">
      @php use Illuminate\Support\Str; @endphp
      <img src="{{ Str::startsWith($item->image, 'http') ? $item->image : asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
    </div>

    <div class="product-detail__info">
      <h1>{{ $item->name }}</h1>

      <p class="brand">ブランド名：{{ ['Armani', 'GU', 'Apple', 'Canon'][array_rand([1, 2, 3, 4])] }}</p>

      <p class="price">
        <span class="price-symbol">¥</span>{{ number_format($item->price) }}
        <span class="tax-label">（税込）</span>
      </p>

      <div class="icons">☆ 3　💬 1</div>

      <a href="{{ route('purchase', $item->id) }}" class="purchase-button">購入手続きへ</a>

      <h2>商品説明</h2>
      @if (!empty($item->color))
      <p>カラー：{{ $item->color }}</p>
      @endif
      <p>{{ $item->description }}</p>

      <h2>商品の情報</h2>
      <p>カテゴリ：
        @foreach (['メンズ', '家電', '日用品', 'アウトドア'] as $category)
        @if (rand(0, 1))
        <span class="tag">{{ $category }}</span>
        @endif
        @endforeach
      </p>

      <p>商品の状態：{{ $item->condition }}</p>

      <h2>コメント ({{ $item->comments->count() }})</h2>

      @foreach ($item->comments as $comment)
      <div class="comment">
        <div class="comment__header">
          <div class="comment__icon"></div>
          <div class="comment__user">{{ $comment->user->name ?? 'ゲスト' }}</div>
        </div>
        <div class="comment__body">{{ $comment->comment }}</div>
      </div>
      @endforeach

      <form action="#" method="POST">
        @csrf
        <textarea name="body" class="comment-textarea" placeholder="商品のコメントを入力してください。"></textarea>
      </form>
    </div>
  </main>
</body>

</html>