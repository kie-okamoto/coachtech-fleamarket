@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 600px; margin: 80px auto; text-align: center;">
  <h2 style="font-size: 28px; color: #2ecc71; margin-bottom: 20px;">決済が完了しました</h2>
  <p style="font-size: 18px; margin-bottom: 32px;">ご購入ありがとうございます！</p>
  <a href="{{ route('index') }}"
    style="display: inline-block; padding: 12px 24px; background-color: #ff5555; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;">
    トップページへ戻る
  </a>
</div>
@endsection