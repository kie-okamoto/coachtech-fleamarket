<header class="header">
    <div class="header__inner">
        <div class="header__left">
            <a href="/" class="header__logo">
                <img src="{{ asset('images/logo.svg') }}" alt="COACHTECHロゴ">
            </a>
        </div>

        <div class="header__center">
            <form action="/" method="GET">
                <input type="text" class="header__search" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
            </form>
        </div>

        <div class="header__right header__nav">
            @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">ログアウト</button>
            </form>
            <a href="/mypage">マイページ</a>
            @else
            <a href="/login">ログイン</a>
            <a href="/mypage">マイページ</a>
            @endauth
            <a href="/sell" class="header__sell">出品</a>
        </div>
    </div>
</header>