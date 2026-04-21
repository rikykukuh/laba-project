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
        'teknisi1_id',
        'teknisi2_id',
        'teknisi3_id',
        'qc_id',
        'state',
    ];

    protected $dates = ['deleted_at'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'id', 'product_id');
    }

    public function orderItemPhotos()
    {
        return $this->hasMany(OrderItemPhoto::class, 'order_item_id', 'id');
    }

    public function teknisi1()
    {
        return $this->belongsTo(User::class, 'teknisi1_id', 'id');
    }

    public function teknisi2()
    {
        return $this->belongsTo(User::class, 'teknisi2_id', 'id');
    }

    public function teknisi3()
    {
        return $this->belongsTo(User::class, 'teknisi3_id', 'id');
    }

    public function qc()
    {
         return $this->belongsTo(User::class, 'qc_id', 'id');
    }
}
