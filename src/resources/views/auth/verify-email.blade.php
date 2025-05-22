<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>メール認証</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/verify.css') }}">
</head>

<body>
  <header class="header">
    <img src="{{ asset('images/logo.svg') }}" alt="COACHTECHロゴ" class="header__logo">
  </header>

  <main class="main">
    <div class="verify-box">
      <p class="verify-message">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
      </p>

      <a href="/" class="verify-button">認証はこちらから</a>

      <form method="POST" action="{{ route('verification.send') }}" class="resend-form">
        @csrf
        <button type="submit" class="resend-link">認証メールを再送する</button>
      </form>
    </div>
  </main>
</body>

</html>