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
use App\Http\Controllers\TradeController;              // 取引画面・メッセージ編集/削除
use App\Http\Controllers\TradeMessageController;       // 取引メッセージ投稿

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
// ▼ トップ（商品一覧）
Route::get('/', [ItemController::class, 'index'])->name('index');

// ▼ 商品詳細
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

/*
|--------------------------------------------------------------------------
| Auth (Register / Login / Logout)
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| Email Verification
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| Authenticated (login required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // ▼ いいね機能
    Route::post('/items/{item}/favorite', [FavoriteController::class, 'store'])->name('favorite.store');
    Route::delete('/items/{item}/favorite', [FavoriteController::class, 'destroy'])->name('favorite.destroy');

    // ▼ コメント機能（※スパム対策で認証必須）
    Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('comment.store');
});

/*
|--------------------------------------------------------------------------
| Auth + Verified (メール認証必須エリア)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // ▼ 取引詳細（チャット）
    Route::get('/trades/{order}', [TradeController::class, 'show'])->name('trades.show');

    // ▼ 取引メッセージ 投稿/編集/削除
    Route::post('/trades/{order}/messages', [TradeMessageController::class, 'store'])->name('trades.messages.store');
    Route::patch('/trades/{order}/messages/{message}', [TradeController::class, 'updateMessage'])->name('trades.messages.update');
    Route::delete('/trades/{order}/messages/{message}', [TradeController::class, 'destroyMessage'])->name('trades.messages.destroy');

    // ▼ 取引完了 → レビュー導線（購入者のみをコントローラ側で制御）
    Route::post('/trades/{order}/finish', [TradeController::class, 'finish'])->name('trades.finish');
    Route::post('/trades/{order}/reviews', [TradeController::class, 'storeReview'])->name('trades.reviews.store');

    // ▼ 商品出品
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');

    // ▼ プロフィール（タブ付きマイページ）
    Route::get('/mypage', [UserController::class, 'profile'])->name('mypage');

    // ▼ プロフィール編集
    Route::get('/mypage/profile', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::post('/mypage/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    // ▼ 商品購入フロー
    Route::get('/purchase/{item_id}', [OrderController::class, 'purchase'])->name('purchase');
    Route::post('/purchase/{item_id}/confirm', [OrderController::class, 'confirm'])->name('purchase.confirm');

    // ▼ 配送先（住所）編集・更新
    Route::get('/purchase/address/{item_id}', [OrderController::class, 'editAddress'])->name('address.edit');
    Route::post('/purchase/address/{item_id}', [OrderController::class, 'updateAddress'])->name('address.update');

    // ▼ Stripe決済
    Route::post('/checkout', [StripeController::class, 'checkout'])->name('stripe.checkout');
    Route::get('/checkout/success', [StripeController::class, 'success'])->name('stripe.success');
    Route::get('/checkout/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');

    // ▼ 注文完了表示
    Route::get('/success', [OrderController::class, 'success'])->name('orders.success');
});
