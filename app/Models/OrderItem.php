<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\ItemType;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_type_id',
        'note',
        'total',
    ];

    public function order(){
        return $this->belongsTo(Order::Class);
    }

    public function itemTypes(){
        return $this->hasMany(ItemType::Class, 'item_type_id', 'id');
    }

    public function orderItemPhotos(){
        return $this->hasMany(OrderItemPhoto::Class, 'order_item_id', 'id');
    }
}
