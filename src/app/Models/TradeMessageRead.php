<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradeMessageRead extends Model
{
    protected $fillable = ['order_id', 'user_id', 'last_read_at'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
