<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\City;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'city_id',
        'phone_number',
    ];

    protected $dates = ['deleted_at'];

    public function city(): HasOne
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }
}
