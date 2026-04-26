<?php

namespace App\Exports;

use App\Models\OrderItemTeknisi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderItemTeknisiExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            'Teknisi',
            'Order Item ID',
            'Nomer BON',
            'Tanggal Bon',
            'Tanggal Assign',
        ];
    }


    public function collection()
    {
        return collect($this->data)->map(function ($item) {

            $order = optional($item->orderItem)->order;

            return [
                'Teknisi' => $item->user->name ?? '-',
                'Order Item ID' => $item->order_item_id,
                'Nomer BON' => optional($order)->number_ticket ?? '-',
                'Tanggal Bon' => optional(optional($order)->created_at)->format('d-m-Y') ?? '-',
                'Tanggal Assign' => optional($item->created_at)->format('d-m-Y') ?? '-',
            ];
        });
    }
}