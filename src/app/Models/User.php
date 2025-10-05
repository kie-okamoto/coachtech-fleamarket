<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\{HasMany, BelongsToMany, HasOne, HasManyThrough};

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /** 一括代入 */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
    ];

    /** 隠し属性 */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** キャスト */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /** 出品アイテム（1:N） */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /** コメント（1:N） */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /** お気に入り（N:N） */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'favorites', 'user_id', 'item_id')
            ->withTimestamps();
    }

    /** 購入した商品（orders 経由の N:N） */
    public function purchasedItems(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'orders', 'user_id', 'item_id')
            ->withTimestamps();
    }

    /** 購入者としての注文（1:N） */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** 自分が出品者の注文（User -> Item -> Order の 1:多:1） */
    public function sellingOrders(): HasManyThrough
    {
        // users.id = items.user_id -> orders.item_id = items.id
        return $this->hasManyThrough(
            Order::class,  // 最終
            Item::class,   // 中間
            'user_id',     // Item側で参照しているUser外部キー
            'item_id',     // Order側で参照しているItem外部キー
            'id',          // Userのローカルキー
            'id'           // Itemのローカルキー
        );
    }

    /** 住所（1:1） */
    public function address(): HasOne
    {
        return $this->hasOne(Address::class, 'user_id', 'id');
    }

    /** 自分が関与する（購入者 or 出品者）注文クエリ */
    public function involvedOrders()
    {
        return Order::query()
            ->where('user_id', $this->id) // 自分が購入者
            ->orWhereHas('item', fn($q) => $q->where('user_id', $this->id)); // 自分が出品者
    }

    /** ヘルパー */
    public function isSellerOf(Item $item): bool
    {
        return (int) $item->user_id === (int) $this->id;
    }

    public function isBuyerOf(Order $order): bool
    {
        return (int) $order->user_id === (int) $this->id;
    }

    /** プロフィール画像URL */
    public function getProfileImageUrlAttribute(): string
    {
        if (!$this->profile_image) {
            return asset('images/no_profile.png'); // プロジェクトに合わせて変更
        }
        return asset('storage/' . ltrim($this->profile_image, '/'));
    }

    /** 取引系 */
    public function tradeMessages(): HasMany
    {
        return $this->hasMany(\App\Models\TradeMessage::class);
    }

    public function receivedReviews(): HasMany  // 受けた評価
    {
        return $this->hasMany(\App\Models\TradeReview::class, 'rated_user_id');
    }

    public function givenReviews(): HasMany     // 自分が与えた評価
    {
        return $this->hasMany(\App\Models\TradeReview::class, 'rater_id');
    }
}
