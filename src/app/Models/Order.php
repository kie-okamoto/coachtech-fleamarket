<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Item;
use App\Models\TradeReview;
use App\Models\TradeMessage;
use App\Models\TradeMessageRead;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'address_id',
        'payment_method',
    ];

    /** 購入者(User) */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** 商品(Item) */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /** 取引メッセージ */
    public function messages()
    {
        return $this->hasMany(TradeMessage::class);
    }

    /** 既読状態 */
    public function readStates()
    {
        return $this->hasMany(TradeMessageRead::class);
    }

    /** この取引に紐づくレビュー */
    public function tradeReviews()
    {
        return $this->hasMany(TradeReview::class);
    }

    /** 購入者(User) エイリアス */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function sellerUser(): ?User
    {
        return optional($this->item)->user;
    }


    public function isReviewedBy(User $user): bool
    {
        return $this->tradeReviews()
            ->where('rater_id', $user->id)
            ->exists();
    }


    public function participants(): array
    {
        $buyerId  = $this->user_id;
        $sellerId = optional($this->item)->user_id;
        return array_values(array_filter([$buyerId, $sellerId]));
    }


    public function isParticipant(User $user): bool
    {
        return in_array($user->id, $this->participants(), true);
    }
}
