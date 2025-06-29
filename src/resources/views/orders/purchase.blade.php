<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>購入手続き</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
</head>

<body>
  @include('components.header')

  <main class="purchase">
    <form action="{{ route('purchase.confirm', ['item_id' => $item->id]) }}" method="POST" class="purchase__form">
      @csrf
      {{-- ▼ 配送先ID --}}
      <input type="hidden" name="address_id" value="{{ $address->id ?? '' }}">

      <div class="purchase__content">
        {{-- 左：商品情報・入力 --}}
        <div class="purchase__left">
          <div class="product-block">
            <div class="product">
              <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
              <div class="product__info">
                <h2>{{ $item->name }}</h2>
                <p>¥{{ number_format($item->price) }}</p>
              </div>
            </div>
          </div>

          {{-- 支払い方法 --}}
          <div class="form-group payment-group">
            <label for="payment_method">支払い方法</label>
            <select name="payment_method" id="payment_method" class="payment-select">
              <option value="">選択してください</option>
              <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>カード払い</option>
              <option value="konbini" {{ old('payment_method') == 'konbini' ? 'selected' : '' }}>コンビニ払い</option>
            </select>
            @error('payment_method')
            <div class="error">{{ $message }}</div>
            @enderror
          </div>

          {{-- 配送先 --}}
          <div class="form-group">
            <p class="form-title">
              <span>配送先</span>
              <a href="{{ url('/purchase/address/' . $item->id) }}">変更する</a>
            </p>

            @if ($address)
            <p>〒{{ $address->postal_code }}</p>
            <p>{{ $address->address }}</p>
            @if (!empty($address->building))
            <p>{{ $address->building }}</p>
            @endif
            @else
            <p class="error">配送先住所が登録されていません。</p>
            @endif

            @error('address_id')
            <div class="error">{{ $message }}</div>
            @enderror
          </div>
        </div>

        {{-- 右：購入概要 --}}
        <div class="purchase__right">
          <div class="summary-grid">
            <div class="summary-cell no-right-border">商品代金</div>
            <div class="summary-cell">¥{{ number_format($item->price) }}</div>

            <div class="summary-cell no-right-border">支払い方法</div>
            <div class="summary-cell">
              <span id="selected-payment-method">未選択</span>
            </div>
          </div>

          <div class="purchase-button-wrapper">
            <button type="submit" class="purchase-button">購入する</button>
          </div>
        </div>
      </div>
    </form>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const select = document.getElementById('payment_method');
      const display = document.getElementById('selected-payment-method');
      const map = {
        'card': 'カード払い',
        'konbini': 'コンビニ払い'
      };
      display.textContent = map[select.value] || '未選択';
      select.addEventListener('change', function() {
        display.textContent = map[this.value] || '未選択';
      });
    });
  </script>
</body>

</html>