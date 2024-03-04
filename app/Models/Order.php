<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\ServiceType;
use App\Models\ItemType;

class Order extends Model
{
    
    protected $fillable = [
        'name', 
        'client_id',
        'item_type_id', 
        'total', 
        'payment', 
        'status',
        'picket_at',
        'picker_by',
        'number_ticket',
        'uang_muka',
        'sisa_pembayaran'
    ];

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function itemType(){
        return $this->belongsTo(ItemType::class, 'item_type_id');
    }

}