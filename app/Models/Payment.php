<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Order;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method_id',
        'payment_merchant_id',
        'value',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::Class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::Class, 'payment_method_id');
    }

    public function paymentMerchant(): BelongsTo
    {
        return $this->belongsTo(PaymentMerchant::Class, 'payment_merchant_id');
    }
}
