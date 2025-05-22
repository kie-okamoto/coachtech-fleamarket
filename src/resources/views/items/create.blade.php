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

      {{-- 画像 --}}
      <section class="form-section">
        <label for="image">商品画像</label>
        <input type="file" name="image" id="image">
      </section>

      {{-- カテゴリ --}}
      <section class="form-section">
        <h2>商品の詳細</h2>
        <label>カテゴリー</label>
        <div class="category-tags">
          @foreach($categories as $category)
          <label class="tag">
            <input type="radio" name="category_id" value="{{ $category->id }}"> {{ $category->name }}
          </label>
          @endforeach
        </div>
      </section>

      {{-- 状態 --}}
      <section class="form-section">
        <label for="condition">商品の状態</label>
        <select name="condition" id="condition">
          <option value="" disabled selected>選択してください</option>
          <option value="良好">良好</option>
          <option value="目立った傷や汚れなし">目立った傷や汚れなし</option>
          <option value="やや傷や汚れあり">やや傷や汚れあり</option>
          <option value="状態が悪い">状態が悪い</option>
        </select>
      </section>

      {{-- 商品名・ブランド・説明・価格 --}}
      <section class="form-section">
        <label for="name">商品名</label>
        <input type="text" name="name" id="name">

        <label for="brand">ブランド名</label>
        <input type="text" name="brand" id="brand">

        <label for="description">商品の説明</label>
        <textarea name="description" id="description" rows="4"></textarea>

        <label for="price">販売価格</label>
        <input type="number" name="price" id="price" min="0">
      </section>

      <button type="submit" class="submit-button">出品する</button>
    </form>
  </main>
</body>

</html>