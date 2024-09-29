<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Order;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'payment_method_id',
        'payment_merchant_id',
        'value',
        'payment_type',
    ];

    protected $dates = ['deleted_at'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function paymentMerchant(): BelongsTo
    {
        return $this->belongsTo(PaymentMerchant::class, 'payment_merchant_id');
    }
}
