<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>プロフィール設定</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/edit_profile.css') }}">
</head>

<body>
  @include('components.header')

  <main class="profile">
    <h1 class="profile__title">プロフィール設定</h1>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile__form">
      @csrf

      {{-- プロフィール画像 --}}
      <div class="profile__image-wrapper">
        <div class="profile__image-column">
          @if (Auth::user()->profile_image)
          <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" class="profile__image" alt="プロフィール画像">
          @else
          <div class="profile__placeholder"></div>
          @endif
        </div>

        <div class="profile__upload-column">
          <label for="profile_image" class="profile__upload-button">画像を選択する</label>
          <input type="file" name="profile_image" id="profile_image" class="profile__file-input">
          @error('profile_image')
          <div class="error">{{ $message }}</div>
          @enderror
        </div>
      </div>

      {{-- ユーザー名 --}}
      <div class="profile__group">
        <label for="name">ユーザー名</label>
        <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}">
        @error('name')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      {{-- 郵便番号 --}}
      <div class="profile__group">
        <label for="postal_code">郵便番号</label>
        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}">
        @error('postal_code')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      {{-- 住所 --}}
      <div class="profile__group">
        <label for="address">住所</label>
        <input type="text" name="address" id="address" value="{{ old('address', $address->address ?? '') }}">
        @error('address')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      {{-- 建物名 --}}
      <div class="profile__group">
        <label for="building">建物名</label>
        <input type="text" name="building" id="building" value="{{ old('building', $address->building ?? '') }}">
        @error('building')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <button type="submit" class="profile__submit">更新する</button>
    </form>
  </main>

  {{-- ✅ プレビュー表示用のJavaScript --}}
  <script>
    document.getElementById('profile_image').addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (!file) return;

      // ✅ ファイル形式チェック：JPEGまたはPNG以外は何も表示しない
      const isJpegOrPng = file.type === 'image/jpeg' || file.type === 'image/png';
      if (!isJpegOrPng) {
        return; // Laravelのバリデーションに任せる
      }

      const reader = new FileReader();
      reader.onload = function(event) {
        const wrapper = document.querySelector('.profile__image-wrapper');
        let img = document.querySelector('.profile__image');

        // プレビュー画像を挿入
        if (img) {
          img.src = event.target.result;
        } else {
          img = document.createElement('img');
          img.classList.add('profile__image');
          img.src = event.target.result;
          wrapper.prepend(img);

          // プレースホルダー削除
          const placeholder = wrapper.querySelector('.profile__placeholder');
          if (placeholder) placeholder.remove();
        }
      };

      reader.readAsDataURL(file);
    });
  </script>


</body>

</html>