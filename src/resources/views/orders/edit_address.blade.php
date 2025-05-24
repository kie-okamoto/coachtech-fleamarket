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

      <!-- 郵便番号 -->
      <div class="form-group">
        <label for="postal_code">郵便番号</label>
        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}">
        @error('postal_code')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <!-- 都道府県 -->
      <div class="form-group">
        <label for="prefecture">都道府県</label>
        <input type="text" name="prefecture" id="prefecture" value="{{ old('prefecture', $address->prefecture ?? '') }}">
        @error('prefecture')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <!-- 市区町村 -->
      <div class="form-group">
        <label for="city">市区町村</label>
        <input type="text" name="city" id="city" value="{{ old('city', $address->city ?? '') }}">
        @error('city')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <!-- 番地・建物名 -->
      <div class="form-group">
        <label for="block">番地・建物名</label>
        <input type="text" name="block" id="block" value="{{ old('block', $address->block ?? '') }}">
        @error('block')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <button type="submit" class="form-submit">更新する</button>
    </form>
  </main>
</body>

</html>