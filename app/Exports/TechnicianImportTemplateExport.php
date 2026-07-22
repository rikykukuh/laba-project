<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\NamedRange;

class TechnicianImportTemplateExport implements WithMultipleSheets, WithEvents
{
    private $technicians;

    public function __construct($technicians)
    {
        $this->technicians = $technicians;
    }

    public function sheets(): array
    {
        return [
            new TechnicianImportTemplateSheet($this->technicians->count()),
            new TechnicianReferenceSheet($this->technicians),
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeWriting::class => function (BeforeWriting $event) {
                $spreadsheet = $event->writer->getDelegate();
                $referenceSheet = $spreadsheet->getSheetByName('Referensi Teknisi');
                $lastRow = max(2, $this->technicians->count() + 1);

                $spreadsheet->addNamedRange(new NamedRange(
                    'DaftarTeknisi',
                    $referenceSheet,
                    '$A$2:$A$' . $lastRow
                ));
            },
        ];
    }
}
