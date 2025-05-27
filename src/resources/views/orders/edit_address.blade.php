<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>住所の変更</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/edit_address.css') }}">
</head>

<body>
  @include('components.header')

  <main class="main">
    <h1 class="form-title">住所の変更</h1>

    <form action="{{ route('address.update', $item->id) }}" method="POST" class="form">
      @csrf

      {{-- 郵便番号 --}}
      <div class="form-group">
        <label for="postal_code">郵便番号</label>
        <input
          type="text"
          name="postal_code"
          id="postal_code"
          value="{{ old('postal_code', $address->postal_code ?? '') }}"
          placeholder="例：123-4567">
        @error('postal_code')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      {{-- 住所 --}}
      <div class="form-group">
        <label for="address">住所</label>
        <input
          type="text"
          name="address"
          id="address"
          value="{{ old('address', $address->address ?? '') }}"
          placeholder="例：東京都新宿区西新宿2-8-1">
        @error('address')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      {{-- 建物名 --}}
      <div class="form-group">
        <label for="building">建物名</label>
        <input
          type="text"
          name="building"
          id="building"
          value="{{ old('building', $address->building ?? '') }}"
          placeholder="例：新宿ビル502号室（任意）">
        @error('building')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <button type="submit" class="form-submit">更新する</button>
    </form>
  </main>
</body>

</html>