<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * 複数代入可能な属性
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
    ];

    /**
     * 非表示にする属性
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * キャスト対象属性
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 出品アイテム（1対多）
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * コメント（1対多）
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * お気に入り（多対多）
     */
    public function favorites()
    {
        return $this->belongsToMany(Item::class, 'favorites', 'user_id', 'item_id')->withTimestamps();
    }

    /**
     * 購入履歴（多対多）
     */
    public function purchasedItems()
    {
        return $this->belongsToMany(Item::class, 'orders', 'user_id', 'item_id')->withTimestamps();
    }

    /**
     * 住所情報（リレーション：1対1）
     */
    public function address()
    {
        return $this->hasOne(Address::class, 'user_id', 'id');
    }
}
