<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ログイン</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
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

  <main class="login">
    <h1 class="login__title">ログイン</h1>

    <form class="login__form" method="POST" action="{{ route('login') }}" novalidate>
      @csrf
      <div class="login__form-group">
        <label for="email">メールアドレス</label>
        <input type="text" name="email" id="email" value="{{ old('email') }}">
        @error('email')
        <p class="error">{{ $message }}</p>
        @enderror
      </div>

      <div class="login__form-group">
        <label for="password">パスワード</label>
        <input type="password" name="password" id="password">
        @error('password')
        <p class="error">{{ $message }}</p>
        @enderror
      </div>


      <button type="submit" class="login__submit">ログインする</button>
    </form>

    <div class="login__link">
      <a href="{{ route('register') }}">会員登録はこちら</a>
    </div>
  </main>
</body>

</html>