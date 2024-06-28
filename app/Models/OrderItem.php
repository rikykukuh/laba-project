<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_id',
        'note',
        'bruto',
        'quantity',
        'discount',
        'netto',
        'vat',
        'transaction_type',
        'total',
    ];

    protected $dates = ['deleted_at'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function itemTypes()
    {
        return $this->hasMany(Product::class, 'product_id', 'id');
    }

    public function orderItemPhotos()
    {
        return $this->hasMany(OrderItemPhoto::class, 'order_item_id', 'id');
    }
}
