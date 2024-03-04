<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\PaymentMethod;

class PaymentMerchant extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_method_id',
        'name'
    ];

    public function paymentMethod(){
        return $this->belongsTo(paymentMethod::Class, 'payment_method_id');
    }


}
