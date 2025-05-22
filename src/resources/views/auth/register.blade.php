<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>会員登録</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>

<body>
  <header class="header">
    <div class="header__inner">
      <div class="header__logo">
        <img src="{{ asset('images/logo.svg') }}" alt="COACHTECHロゴ">
      </div>
    </div>
  </header>

  <main class="main">
    <h2 class="form-title">会員登録</h2>

    {{-- ▼ エラーメッセージ表示 --}}
    @if ($errors->any())
    <div class="form-errors">
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <form class="form" action="{{ route('register') }}" method="POST">
      @csrf

      <div class="form-group">
        <label for="name">ユーザー名</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}" required>
      </div>

      <div class="form-group">
        <label for="email">メールアドレス</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required>
      </div>

      <div class="form-group">
        <label for="password">パスワード</label>
        <input type="password" name="password" id="password" required>
      </div>

      <div class="form-group">
        <label for="password_confirmation">確認用パスワード</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required>
      </div>

      <button type="submit" class="form-submit">登録する</button>
    </form>

    <div class="form-footer">
      <a href="/login">ログインはこちら</a>
    </div>
  </main>
</body>

</html>