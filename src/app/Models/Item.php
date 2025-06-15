<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
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

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function getIsSoldAttribute()
    {
        return $this->order()->exists();
    }

    public function isSold()
    {
        return $this->order !== null;
    }
}
