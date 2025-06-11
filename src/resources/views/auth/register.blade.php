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
        <a href="{{ url('/') }}">
          <img src="{{ asset('images/logo.svg') }}" alt="COACHTECHロゴ">
        </a>
      </div>
    </div>
  </header>


  <main class="main">
    <h2 class="form-title">会員登録</h2>

    <form class="form" action="{{ route('register') }}" method="POST">
      @csrf

      {{-- ユーザー名 --}}
      <div class="form-group">
        <label for="name">ユーザー名</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}">
        @error('name')
        <p class="error">{{ $message }}</p>
        @enderror
      </div>

      {{-- メールアドレス --}}
      <div class="form-group">
        <label for="email">メールアドレス</label>
        <input type="text" name="email" id="email" value="{{ old('email') }}">
        @error('email')
        <p class="error">{{ $message }}</p>
        @enderror
      </div>

      {{-- パスワード --}}
      <div class="form-group">
        <label for="password">パスワード</label>
        <input type="password" name="password" id="password">
        @error('password')
        <p class="error">{{ $message }}</p>
        @enderror
      </div>

      {{-- 確認用パスワード --}}
      <div class="form-group">
        <label for="password_confirmation">確認用パスワード</label>
        <input type="password" name="password_confirmation" id="password_confirmation">
        @error('password_confirmation')
        <p class="error">{{ $message }}</p>
        @enderror
      </div>

      <button type="submit" class="form-submit">登録する</button>
    </form>

    <div class="form-footer">
      <a href="/login">ログインはこちら</a>
    </div>
  </main>
</body>

</html>