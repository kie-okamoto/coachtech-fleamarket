<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    /**
     * ログインフォームの表示
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * ログイン処理
     */
    public function login(LoginRequest $request) // ← LoginRequest に変更
    {
        $credentials = $request->validated(); // バリデーション済みデータ取得

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        // 入力情報が誤っていた場合のエラーメッセージ
        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません。',
        ])->onlyInput('email');
    }

    /**
     * 会員登録フォームの表示
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * 会員登録処理（メール認証対応）
     */
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user)); // Fortifyのメール認証イベント発火
        Auth::login($user);

        return redirect('/email/verify'); // 認証待ち画面へ
    }

    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
