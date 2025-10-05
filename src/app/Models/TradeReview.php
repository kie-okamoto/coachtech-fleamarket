<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradeReview extends Model
{
    protected $fillable = [
        'order_id',
        'rater_id',
        'rated_user_id',
        'score',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_id');
    }
    public function ratedUser()
    {
        return $this->belongsTo(User::class, 'rated_user_id');
    }
}
