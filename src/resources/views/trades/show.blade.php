{{-- resources/views/trades/show.blade.php --}}
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>取引画面</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/trade.css') }}">

</head>

<body>
  @include('components.header')

  @php
  $me = auth()->user();
  $isBuyer = $me && ($me->id === $order->user_id);

  $item = $order->item;
  $seller = $item ? $item->user : null;
  $buyer = $order->user;
  $partner = $isBuyer ? $seller : $buyer;

  $noProfile = asset('images/no_profile.png');
  $meIcon = ($me && $me->profile_image) ? asset('storage/'.$me->profile_image) : $noProfile;
  $partnerIcon = ($partner && $partner->profile_image) ? asset('storage/'.$partner->profile_image) : $noProfile;
  $itemImg = ($item && $item->image) ? asset('storage/'.$item->image) : null;
  @endphp

  <div class="trade-layout">
    {{-- 左サイド：その他の取引 --}}
    <aside class="trade-aside">
      <h2 class="trade-aside__title">その他の取引</h2>
      <ul class="trade-aside__list">
        @forelse (($otherOrders ?? collect()) as $o)
        @php
        $name = optional($o->item)->name ?: ("取引 #".$o->id);
        $thumb = ($o->item && $o->item->image) ? asset('storage/'.$o->item->image) : asset('images/no-image.png');
        $unread = (int)($o->unread_count ?? 0);
        @endphp
        <li class="trade-aside__item">
          <a href="{{ route('trades.show', $o) }}" class="trade-aside__link" title="{{ $name }}">
            <img src="{{ $thumb }}" alt="" class="trade-aside__thumb" onerror="this.src='{{ asset('images/no-image.png') }}'">
            <span class="trade-aside__text">{{ \Illuminate\Support\Str::limit($name, 18) }}</span>
            @if($unread > 0)
            <span class="trade-aside__badge" aria-label="未読件数">{{ $unread }}</span>
            @endif
          </a>
        </li>
        @empty
        <li class="trade-aside__empty">他の取引はありません</li>
        @endforelse
      </ul>
    </aside>

    {{-- 右メイン --}}
    <main class="trade-main">
      <header class="trade-header">
        <div class="trade-header__left">
          <img class="avatar" src="{{ $partnerIcon }}" alt="相手のアイコン">
          <h1 class="trade-title">「{{ $partner ? $partner->name : 'ユーザー名' }}」さんとの取引画面</h1>
        </div>

        {{-- 取引完了ボタン（購入者・未完了のみ） --}}
        @if($isBuyer && is_null($order->completed_at))
        <form method="POST" action="{{ route('trades.finish', $order) }}">
          @csrf
          <button class="trade-finish-btn" type="submit">取引を完了する</button>
        </form>
        @else
        <span class="trade-finish-badge" aria-label="取引完了済み">取引完了</span>
        @endif
      </header>

      @if (session('status'))
      <div class="flash flash--success" role="status" aria-live="polite">
        {{ session('status') }}
      </div>
      @endif

      {{-- 商品概要 --}}
      <section class="trade-product">
        <div class="trade-product__thumb">
          @if ($itemImg)
          <img src="{{ $itemImg }}" alt="{{ $item ? $item->name : '商品画像' }}">
          @else
          <span class="trade-product__noimg">商品画像</span>
          @endif
        </div>
        <div>
          <h2 class="trade-product__name">{{ $item ? $item->name : '商品名' }}</h2>
          <p class="trade-product__price">
            @if ($item) ¥{{ number_format($item->price) }} @else 商品価格 @endif
          </p>
        </div>
      </section>

      {{-- チャット --}}
      <section class="trade-chat">
        <ul class="chat-list">
          @foreach ($messages as $msg)
          @php
          $isMine = auth()->id() === $msg->user_id;
          $user = $msg->user;
          $userName = $user ? $user->name : 'ユーザー';
          $icon = ($user && $user->profile_image) ? asset('storage/'.$user->profile_image) : asset('images/no_profile.png');
          @endphp

          <li class="chat-row">
            {{-- ★ 左右どちらも「上：アイコン＋名前」「下：メッセージ」構造に統一 --}}
            <div class="chat-block {{ $isMine ? 'chat-block--right' : 'chat-block--left' }}">

              {{-- 上段：メタ行（左＝アイコン→名前 / 右＝名前→アイコン） --}}
              <div class="chat-meta-line {{ $isMine ? 'chat-meta-line--right' : '' }}">
                @if($isMine)
                <span class="chat-name">{{ $userName }}</span>
                <img src="{{ $icon }}" alt="{{ $userName }} のアイコン" class="chat-avatar-img">
                @else
                <img src="{{ $icon }}" alt="{{ $userName }} のアイコン" class="chat-avatar-img">
                <span class="chat-name">{{ $userName }}</span>
                @endif
              </div>

              {{-- 下段：本文（吹き出し） --}}
              <div class="chat-bubble {{ $isMine ? 'chat-bubble--mine' : '' }}" id="bubble-{{ $msg->id }}">
                {{ $msg->body }}
                @if ($msg->image_path)
                <div style="margin-top:8px;">
                  <img src="{{ asset('storage/'.$msg->image_path) }}" alt="添付画像" style="max-width:320px;height:auto;border-radius:8px;">
                </div>
                @endif
              </div>

              {{-- 自分のメッセージのみ：編集・削除 --}}
              @if ($isMine)
              <form id="edit-form-{{ $msg->id }}"
                class="chat-bubble chat-bubble--mine"
                style="display:none; max-width:760px;"
                method="post"
                action="{{ route('trades.messages.update', [$order, $msg]) }}">
                @csrf
                @method('PATCH')
                <textarea name="body" rows="3" class="chat-edit__input" required>{{ old('body', $msg->body) }}</textarea>
                <div class="chat-edit__actions">
                  <button type="submit" class="btn btn--update">更新</button>
                  <button type="button" class="btn btn--cancel" onclick="toggleEdit({{ $msg->id }}, false)">キャンセル</button>
                </div>
              </form>

              <div class="chat-actions">
                <button class="chat-action" type="button" onclick="toggleEdit({{ $msg->id }}, true)">編集</button>
                <form method="post" action="{{ route('trades.messages.destroy', [$order, $msg]) }}"
                  onsubmit="return confirm('このメッセージを削除しますか？')" style="display:inline;">
                  @csrf
                  @method('DELETE')
                  <button class="chat-action" type="submit">削除</button>
                </form>
              </div>
              @endif

            </div>
          </li>
          @endforeach
        </ul>

        {{-- 下書き設定（JS用） --}}
        <div id="draft-config"
          data-order-id="{{ $order->id }}"
          data-uid="{{ auth()->id() ?? '' }}"
          data-has-old="{{ old('body') ? '1' : '0' }}"></div>

        {{-- 新規投稿フォーム --}}
        <form class="chat-form" method="post" action="{{ route('trades.messages.store', $order) }}" enctype="multipart/form-data">
          @csrf
          <div class="chat-error" role="alert" aria-live="polite">
            @error('body') {{ $message }} @enderror
            @error('image') {{ $message }} @enderror
          </div>

          <div class="chat-controls">
            <input type="text" name="body" id="chatBody" class="chat-input"
              placeholder="取引メッセージを記入してください" value="{{ old('body') }}">
            <input id="chatImage" type="file" name="image" hidden>
            <button type="button" class="chat-add-image" onclick="document.getElementById('chatImage').click()">画像を追加</button>
            <button type="submit" class="chat-send" aria-label="送信">
              <img src="{{ asset('images/send.svg') }}" alt="送信" class="chat-send-icon">
            </button>
          </div>
        </form>
      </section>
    </main>
  </div>

  {{-- 評価モーダル --}}
  @if (($show_review_modal ?? false) || session('show_review_modal'))
  <div id="reviewModal" class="modal is-active" aria-hidden="false">
    <div class="modal__overlay" data-modal-close></div>
    <div class="modal__content review-panel" role="dialog" aria-modal="true" aria-labelledby="reviewModalTitle">
      <h2 id="reviewModalTitle" class="review-title">取引が完了しました。</h2>
      <p class="review-subtitle">今回の取引相手はどうでしたか？</p>

      @if ($errors->has('score'))
      <div class="form-error" role="alert" aria-live="polite" style="margin-bottom:8px;">
        @error('score') <div>{{ $message }}</div> @enderror
      </div>
      @endif

      <form method="POST" action="{{ route('trades.reviews.store', $order) }}" id="reviewForm">
        @csrf
        <div class="rating" aria-label="星で評価（1〜5）">
          @for($i = 5; $i >= 1; $i--)
          <input type="radio" name="score" id="star{{ $i }}" value="{{ $i }}" @checked(old('score')==$i) required>
          <label for="star{{ $i }}">★</label>
          @endfor
        </div>
        <div class="modal__actions">
          <button type="submit" class="btn btn-primary" id="reviewSubmit">送信する</button>
        </div>
      </form>
    </div>
  </div>
  @endif

  <script>
    function toggleEdit(id, show) {
      const form = document.getElementById('edit-form-' + id);
      const bubble = document.getElementById('bubble-' + id);
      if (!form || !bubble) return;
      form.style.display = show ? 'block' : 'none';
      bubble.style.display = show ? 'none' : 'block';
    }

    // 本文の下書き保持（localStorage）
    (function() {
      const input = document.getElementById('chatBody');
      if (!input) return;

      const cfg = document.getElementById('draft-config');
      const orderId = (cfg && cfg.dataset.orderId) ? cfg.dataset.orderId : '';
      const uid = (cfg && cfg.dataset.uid) ? cfg.dataset.uid : '';
      const hasOld = (cfg && cfg.dataset.hasOld === '1');
      const key = `trade_draft_body_order_${orderId}${uid ? `_user_${uid}` : ''}`;

      document.addEventListener('DOMContentLoaded', () => {
        if (!hasOld) {
          const saved = localStorage.getItem(key);
          if (saved !== null) input.value = saved;
        } else {
          localStorage.setItem(key, input.value);
        }
      });

      input.addEventListener('input', () => localStorage.setItem(key, input.value));
      const form = input.closest('form');
      if (form) form.addEventListener('submit', () => localStorage.removeItem(key));
      window.addEventListener('storage', (e) => {
        if (e.key === key && e.storageArea === localStorage) input.value = e.newValue || '';
      });
    })();

    // モーダル制御 & 二重送信防止
    (function() {
      const modal = document.getElementById('reviewModal');
      if (!modal) return;
      const close = () => {
        modal.classList.remove('is-active');
        modal.setAttribute('aria-hidden', 'true');
      };
      modal.addEventListener('click', (e) => {
        if (e.target.matches('[data-modal-close], .modal__overlay')) close();
      });
      const form = document.getElementById('reviewForm');
      const submitBtn = document.getElementById('reviewSubmit');
      if (form && submitBtn) form.addEventListener('submit', () => {
        submitBtn.disabled = true;
      });
    })();
  </script>
</body>

</html>