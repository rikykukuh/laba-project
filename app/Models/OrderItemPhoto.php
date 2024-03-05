<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\OrderItem;

class OrderItemPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'thumbnail_url',
        'preview_url',
    ];

    public function orderItem(){
        return $this->belongsTo(orderItem::Class, 'orderitem_id');
    }
}
