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

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
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

    public function teknisis()
    {
        return $this->belongsToMany(User::class, 'order_item_teknisi')->withTimestamps();;
    }

    public static function sequenceLabel(int $position): string
    {
        if ($position < 1) {
            return '';
        }

        $label = '';

        while ($position > 0) {
            $position--;
            $label = chr(65 + ($position % 26)) . $label;
            $position = intdiv($position, 26);
        }

        return $label;
    }

    public static function sequencePosition(string $label): ?int
    {
        $label = strtoupper(trim($label));

        if ($label === '' || !preg_match('/^[A-Z]+$/', $label)) {
            return null;
        }

        $position = 0;

        foreach (str_split($label) as $letter) {
            $position = ($position * 26) + (ord($letter) - 64);
        }

        return $position;
    }

    public function sequencePositionInOrder(): ?int
    {
        if (!$this->order) {
            return null;
        }

        $index = $this->order->orderItems->search(function (OrderItem $item) {
            return (int) $item->id === (int) $this->id;
        });

        return $index === false ? null : $index + 1;
    }

    public function importCode(): ?string
    {
        $position = $this->sequencePositionInOrder();

        return $position && $this->order
            ? $this->order->itemImportCode($position)
            : null;
    }
}
