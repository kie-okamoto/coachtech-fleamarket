<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>住所の変更</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/address.css') }}">
</head>

<body>
  @include('components.header')

  <main class="main">
    <h1 class="form-title">住所の変更</h1>
    <form action="{{ route('address.update', $item->id ?? 1) }}" method="POST" class="form">
      @csrf

      <div class="form-group">
        <label for="postal_code">郵便番号</label>
        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}">
        @error('postal_code')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label for="address">住所</label>
        <input type="text" name="address" id="address" value="{{ old('address') }}">
        @error('address')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label for="building">建物名</label>
        <input type="text" name="building" id="building" value="{{ old('building') }}">
        @error('building')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <button type="submit" class="form-submit">更新する</button>
    </form>
  </main>
</body>

</html>