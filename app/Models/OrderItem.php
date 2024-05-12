<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\ItemType;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'item_type_id',
        'note',
        'total',
    ];

    protected $dates = ['deleted_at'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function itemTypes()
    {
        return $this->hasMany(ItemType::class, 'item_type_id', 'id');
    }

    public function orderItemPhotos()
    {
        return $this->hasMany(OrderItemPhoto::class, 'order_item_id', 'id');
    }
}
