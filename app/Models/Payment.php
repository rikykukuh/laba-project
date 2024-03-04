<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\paymentMerchant;
use App\Models\paymentMethod;
use App\Models\Order;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method_id',
        'payment_merchant_id',
        'value',
    ];

    public function order(){
        return $this->belongsTo(Order::Class);
    }

    public function paymentMethod(){
        return $this->belongsTo(paymentMethod::Class, 'payment_method_id');
    }

    public function paymentMerchant(){
        return $this->belongsTo(paymentMerchant::Class, 'payment_merchant_id');
    }
}
