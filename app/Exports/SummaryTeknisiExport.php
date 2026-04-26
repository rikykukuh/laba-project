<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SummaryTeknisiExport implements FromCollection, withHeadings
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
            'Total',
            'Masuk',
            'Proses',
            'Selesai',
            'Cancel',
        ];
    }

    public function collection()
    {
        return collect($this->data)->map(function ($s) {
            return [
                'Teknisi' => $s->user->name ?? '-',
                'Total' => $s->total,
                'Masuk' => $s->masuk,
                'Proses' => $s->proses,
                'Selesai' => $s->selesai,
                'Cancel' => $s->cancel,
            ];
        });
    }
}