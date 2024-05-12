<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMerchant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_method_id',
        'name'
    ];

    protected $dates = ['deleted_at'];

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
