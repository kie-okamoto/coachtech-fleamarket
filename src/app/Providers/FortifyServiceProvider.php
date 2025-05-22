<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Actions\Auth\RedirectToProfileAfterRegister;
use App\Actions\Auth\RedirectToProfileAfterLogin;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LoginResponse;

class FortifyServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // register.blade.phpを表示するための設定
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // login.blade.php を表示するための設定
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // 登録後のリダイレクト先
        $this->app->singleton(RegisterResponse::class, RedirectToProfileAfterRegister::class);

        // ログイン後のリダイレクト先
        $this->app->singleton(LoginResponse::class, RedirectToProfileAfterLogin::class);
    }
}
