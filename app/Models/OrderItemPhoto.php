<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItemPhoto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_item_id',
        'thumbnail_url',
        'preview_url',
    ];

    protected $dates = ['deleted_at'];

    public function orderItem()
    {
        return $this->belongsTo(orderItem::class, 'orderitem_id');
    }
}
