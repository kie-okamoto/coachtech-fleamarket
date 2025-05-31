<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;

// ▼ トップ画面（商品一覧・マイリスト）
Route::get('/', [ItemController::class, 'index'])->name('index');

// ▼ 会員登録・ログイン関連
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// ▼ 商品詳細
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

// ▼ いいね機能
Route::middleware('auth')->group(function () {
    Route::post('/items/{item}/favorite', [FavoriteController::class, 'store'])->name('favorite.store');
    Route::delete('/items/{item}/favorite', [FavoriteController::class, 'destroy'])->name('favorite.destroy');
});


// ▼ コメント機能
Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('comment.store');

// ▼ 商品購入
Route::get('/purchase/{item_id}', [OrderController::class, 'purchase'])->name('purchase');
Route::post('/purchase/{item_id}/confirm', [OrderController::class, 'confirm'])->name('purchase.confirm');

// ▼ 配送先（住所）編集・更新
Route::get('/purchase/address/{item_id}', [OrderController::class, 'editAddress'])->name('address.edit');
Route::post('/purchase/address/{item_id}', [OrderController::class, 'updateAddress'])->name('address.update');

// ▼ Stripe決済関連
Route::post('/checkout', [StripeController::class, 'checkout'])->name('stripe.checkout');
Route::get('/checkout/success', [StripeController::class, 'success'])->name('stripe.success');
Route::get('/checkout/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');

// ▼ 商品出品画面
Route::get('/sell', [ItemController::class, 'create'])->middleware(['auth', 'verified'])->name('items.create');
Route::post('/sell', [ItemController::class, 'store'])->middleware(['auth', 'verified'])->name('items.store');

// ▼ プロフィール画面（タブ切替付き）
Route::get('/mypage', [UserController::class, 'profile'])->middleware(['auth', 'verified'])->name('mypage');

// ▼ プロフィール編集
Route::get('/mypage/profile', [UserController::class, 'editProfile'])->middleware(['auth', 'verified'])->name('profile.edit');
Route::post('/mypage/profile', [UserController::class, 'updateProfile'])->middleware(['auth', 'verified'])->name('profile.update');

// ▼ メール認証関連
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware(['auth'])->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('profile.edit')->with('message', 'メールアドレスが認証されました');
})->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証メールを再送しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
