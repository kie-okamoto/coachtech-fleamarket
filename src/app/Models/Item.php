<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'condition',
        'price',
        'user_id',
    ];

    // コメントとのリレーション
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // 投稿ユーザーとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // カテゴリとのリレーション
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }


    // お気に入り
    public function favoritedUsers()
    {
        return $this->belongsToMany(User::class, 'favorites', 'item_id', 'user_id')->withTimestamps();
    }

    // ✅ 購入者（1対1リレーション・リレーション名を修正）
    public function order()
    {
        return $this->hasOne(Order::class);
    }

    // ✅ 購入済みフラグ（order()と統一）
    public function getIsSoldAttribute()
    {
        return $this->order !== null;
    }

    public function isSold()
    {
        return $this->order !== null;
    }
}
