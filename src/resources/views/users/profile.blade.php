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


  <main class="profile">
    <div class="profile__user">
      <div class="profile__icon"></div>
      <div class="profile__name">ユーザー名</div>
      <a href="/profile/edit" class="profile__edit-button">プロフィールを編集</a>
    </div>

    <div class="tabs">
      <a href="#" class="tab active">出品した商品</a>
      <a href="#" class="tab">購入した商品</a>
    </div>
    <hr class="divider">

    <div class="product-grid">
      @for ($i = 0; $i < 8; $i++)
        <div class="product-card">
        <div class="product-image">商品画像</div>
        <p class="product-name">商品名</p>
    </div>
    @endfor
    </div>
  </main>
</body>

</html>