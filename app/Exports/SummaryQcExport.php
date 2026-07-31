<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SummaryQcExport implements FromCollection, WithHeadings
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return ['QC', 'Total', 'Masuk', 'Proses', 'Selesai', 'Gudang A', 'Gudang B', 'Gudang C', 'Cancel', 'Belum Ada State'];
    }

    public function collection()
    {
        return collect($this->data)->map(function ($row) {
            return [
                $row->qc->name ?? '-',
                $row->total,
                $row->masuk,
                $row->proses,
                $row->selesai,
                $row->gudang_a,
                $row->gudang_b,
                $row->gudang_c,
                $row->cancel,
                $row->belum_ada_state,
            ];
        });
    }
}
