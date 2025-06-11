<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>決済完了</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/success.css') }}">
</head>

<body>
  <header class="header">
    <div class="header__logo">
      <a href="{{ url('/') }}">
        <img src="{{ asset('images/logo.svg') }}" alt="COACHTECHロゴ">
      </a>
    </div>
  </header>

  <main class="success">
    <div class="success__inner">
      <h2 class="success__title">THANK YOU</h2>
      <p class="success__message">決済が完了しました</p>
      <a href="{{ route('index') }}" class="success__link">TOP</a>
    </div>
  </main>
</body>

</html>