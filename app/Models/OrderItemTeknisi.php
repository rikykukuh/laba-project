<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemTeknisi extends Model
{
    protected $table = 'order_item_teknisi';

    protected $fillable = [
        'order_item_id',
        'user_id'
    ];

    public function teknisis()
    {
        return $this->belongsToMany(User::class, 'order_item_teknisi')->withTimestamps();;
    }

}