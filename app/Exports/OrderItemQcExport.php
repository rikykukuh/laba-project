<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderItemQcExport implements FromCollection, WithHeadings
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return ['QC', 'Kode Bon Item', 'Nomer BON', 'Tanggal Bon', 'Tanggal Assign'];
    }

    public function collection()
    {
        return collect($this->data)->map(function ($item) {
            return [
                $item->qc->name ?? '-',
                $item->importCode() ?? '-',
                optional($item->order)->number_ticket ?? '-',
                optional(optional($item->order)->created_at)->format('d-m-Y') ?? '-',
                optional($item->updated_at)->format('d-m-Y') ?? '-',
            ];
        });
    }
}
