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
        // 'completed_at' は手動更新想定のため fillable には含めないでOK
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

    /**
     * 出品者(User) を返すヘルパ
     * Item が未設定のケースに備えて null セーフに。
     */
    public function sellerUser(): ?User
    {
        return optional($this->item)->user;
    }

    /**
     * この取引を指定ユーザーが既に評価済みか？
     */
    public function isReviewedBy(User $user): bool
    {
        return $this->tradeReviews()
            ->where('rater_id', $user->id)
            ->exists();
    }

    /**
     * 取引参加者（購入者＋出品者）のユーザーID配列
     */
    public function participants(): array
    {
        $buyerId  = $this->user_id;
        $sellerId = optional($this->item)->user_id; // item 未設定でも安全
        return array_values(array_filter([$buyerId, $sellerId]));
    }

    /**
     * 指定ユーザーがこの取引の参加者か？
     */
    public function isParticipant(User $user): bool
    {
        return in_array($user->id, $this->participants(), true);
    }
}
