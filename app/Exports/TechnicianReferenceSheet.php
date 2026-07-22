<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TechnicianReferenceSheet implements FromArray, ShouldAutoSize, WithEvents, WithTitle
{
    private $technicians;

    public function __construct($technicians)
    {
        $this->technicians = $technicians;
    }

    public function array(): array
    {
        $rows = [['Nama Teknisi']];

        foreach ($this->technicians as $technician) {
            $rows[] = [$technician->name];
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Referensi Teknisi';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
            },
        ];
    }
}
