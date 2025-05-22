<?php

namespace App\Actions\Auth;

use Laravel\Fortify\Contracts\LoginResponse;
use Illuminate\Http\Request;

class RedirectToProfileAfterLogin implements LoginResponse
{
  public function toResponse($request)
  {
    // 初回ログイン後にプロフィール設定画面へリダイレクト
    return redirect('/mypage/profile');
  }
}
