<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TechnicianImportTemplateSheet implements FromArray, ShouldAutoSize, WithEvents, WithStyles, WithTitle
{
    private $technicianCount;

    public function __construct(int $technicianCount)
    {
        $this->technicianCount = $technicianCount;
    }

    public function array(): array
    {
        $rows = [[
            'No Bon',
            'ID Barang',
            'Nama Teknisi',
            'Tanggal Dikerjakan',
            'Tanggal Selesai',
        ]];

        for ($row = 0; $row < 100; $row++) {
            $rows[] = [null, null, null, null, null];
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Import Teknisi';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF3C8DBC'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:E101');
                $sheet->getStyle('D2:E501')->getNumberFormat()->setFormatCode('dd-mm-yyyy');
                $sheet->getStyle('A2:A501')->getNumberFormat()->setFormatCode('@');
                $sheet->getStyle('A1:E101')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension(1)->setRowHeight(24);
                $sheet->getColumnDimension('A')->setWidth(20);
                $sheet->getColumnDimension('B')->setWidth(14);
                $sheet->getColumnDimension('C')->setWidth(28);
                $sheet->getColumnDimension('D')->setWidth(22);
                $sheet->getColumnDimension('E')->setWidth(20);

                $validation = new DataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowDropDown(true);
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Teknisi tidak valid');
                $validation->setError('Pilih teknisi dari daftar yang tersedia.');
                $validation->setShowInputMessage(true);
                $validation->setPromptTitle('Pilih teknisi');
                $validation->setPrompt(
                    $this->technicianCount > 0
                        ? 'Pilih nama teknisi atau QC dari daftar.'
                        : 'Belum ada user dengan role teknisi atau qc_user.'
                );
                $validation->setFormula1('=DaftarTeknisi');

                for ($row = 2; $row <= 501; $row++) {
                    $sheet->getCell('C' . $row)->setDataValidation(clone $validation);
                }

                $sheet->getComment('A1')->getText()->createTextRun('Nomor bon harus sesuai dengan ID Barang.');
                $sheet->getComment('B1')->getText()->createTextRun('Gunakan ID Barang yang tampil pada detail service.');
                $sheet->getComment('C1')->getText()->createTextRun('Wajib dipilih dari dropdown.');
                $sheet->getComment('D1')->getText()->createTextRun('Tanggal teknisi mulai mengerjakan barang.');
                $sheet->getComment('E1')->getText()->createTextRun('Boleh dikosongkan. Saat import, nilai kosong otomatis menggunakan tanggal hari ini.');
            },
        ];
    }
}
