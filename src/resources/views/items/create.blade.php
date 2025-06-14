<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>商品の出品</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/create.css') }}">
</head>

<body>
  @include('components.header')

  <main class="create">
    <h1 class="create__title">商品の出品</h1>

    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="create__form">
      @csrf

      {{-- 商品画像 --}}
      <div class="form-section">
        <label for="image">商品画像</label>
        <div class="image-upload-area">
          <label class="image-upload-button" for="image">画像を選択する</label>
          <input type="file" id="image" name="image" accept="image/*">
          <img id="create-preview-image" src="" alt="プレビュー画像">
        </div>
        @error('image')
        <p class="error">{{ $message }}</p>
        @enderror
      </div>

      {{-- カテゴリ --}}
      <section class="form-section">
        <h2>商品の詳細</h2>
        <div class="section-divider"></div>
        <label>カテゴリー</label>
        <div class="category-tags">
          @foreach($categories->slice(0, 12) as $category)
          <label class="tag">
            <input type="checkbox" name="categories[]" value="{{ $category->id }}" {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
            <span>{{ $category->name }}</span>
          </label>
          @endforeach
        </div>

        <div class="category-tags category-tags--left">
          @foreach($categories->slice(12) as $category)
          <label class="tag">
            <input type="checkbox" name="categories[]" value="{{ $category->id }}" {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
            <span>{{ $category->name }}</span>
          </label>
          @endforeach
        </div>


        @error('categories')
        <div class="category-error">{{ $message }}</div>
        @enderror
      </section>

      {{-- 商品の状態 --}}
      <section class="form-section">
        <label for="condition">商品の状態</label>
        <select name="condition" id="condition" class="condition-select">
          <option value="" disabled {{ old('condition') === null ? 'selected' : '' }}>選択してください</option>
          <option value="良好" {{ old('condition') === '良好' ? 'selected' : '' }}>良好</option>
          <option value="目立った傷や汚れなし" {{ old('condition') === '目立った傷や汚れなし' ? 'selected' : '' }}>目立った傷や汚れなし</option>
          <option value="やや傷や汚れあり" {{ old('condition') === 'やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
          <option value="状態が悪い" {{ old('condition') === '状態が悪い' ? 'selected' : '' }}>状態が悪い</option>
        </select>
        @error('condition')
        <p class="error">{{ $message }}</p>
        @enderror
      </section>

      {{-- 商品名・説明 --}}
      <section class="form-section">
        <h2>商品名と説明</h2>
        <div class="section-divider"></div>

        <label for="name">商品名</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}">
        @error('name')
        <p class="error">{{ $message }}</p>
        @enderror

        <label for="brand">ブランド名</label>
        <input type="text" name="brand" id="brand" value="{{ old('brand') }}">

        <label for="description">商品の説明</label>
        <textarea name="description" id="description" rows="4">{{ old('description') }}</textarea>
        @error('description')
        <p class="error">{{ $message }}</p>
        @enderror

        <label for="price">販売価格</label>
        <input
          type="text"
          name="price"
          id="price"
          value="{{ old('price') }}"
          class="{{ $errors->has('price') ? 'input-error' : '' }}">
        @error('price')
        <p class="error">{{ $message }}</p>
        @enderror

      </section>

      <button type="submit" class="submit-button">出品する</button>
    </form>
  </main>

  {{-- JavaScript --}}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const categoryInputs = document.querySelectorAll('.category-tags input[type="checkbox"]');
      categoryInputs.forEach(input => {
        input.addEventListener('change', function() {
          this.closest('label').classList.toggle('tag--selected', this.checked);
        });
        if (input.checked) {
          input.closest('label').classList.add('tag--selected');
        }
      });

      const imageInput = document.getElementById('image');
      const previewImage = document.getElementById('create-preview-image');
      imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file && file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewImage.style.display = 'block';
          };
          reader.readAsDataURL(file);
        }
      });
    });
  </script>
</body>

</html>