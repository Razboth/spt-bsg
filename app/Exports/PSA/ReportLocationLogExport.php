<?php

namespace App\Exports\PSA;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ReportLocationLogExport implements WithCustomStartCell, WithEvents
{
    protected array $data;

    function __construct($data)
    {
        $this->data = $data;
    }

    public function registerEvents(): array
    {

        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $sheet->getDelegate()->getStyle('A1:A2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                ]);
                $sheet->getDelegate()->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->setCellValue("A1", 'Rekap Aktifitas Lokasi Sales');

                $sheet->getDelegate()->getStyle('A4:H5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getDelegate()->getStyle('A4:H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getDelegate()->getStyle('A4:H5')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    "borders" => [
                        "allBorders" => [
                            "borderStyle" =>
                                Border::BORDER_THIN,
                            "color" => ["argb" => "000000"],
                        ],
                    ],
                ]);

                $sheet->getRowDimension(4)->setRowHeight(30);
                $sheet->getRowDimension(5)->setRowHeight(30);

                $sheet->mergeCells("A1:B1");

                $sheet->mergeCells("A4:A5");
                $sheet->mergeCells("B4:B5");
                $sheet->mergeCells("C4:C5");
                $sheet->mergeCells("D4:D5");
                $sheet->mergeCells("E4:E5");
                $sheet->mergeCells("F4:H4");

                $sheet->setCellValue("A4", 'NO');
                $sheet->setCellValue("B4", 'NAMA');
                $sheet->setCellValue("C4", 'CABANG');
                $sheet->setCellValue("D4", 'JABATAN');
                $sheet->setCellValue("E4", 'WAKTU');
                $sheet->setCellValue("F4", 'LOKASI');

                $sheet->setCellValue("F5", 'LONGITUDE');
                $sheet->setCellValue("G5", 'LATITUDE');
                $sheet->setCellValue("H5", 'ALAMAT');

                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(40);
                $sheet->getColumnDimension('C')->setWidth(30);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(60);

                $currentRow = 6;
                $no = 0;
                foreach ($this->data['logs'] as $log){
                    $no++;
                    $sheet->setCellValue("A" . $currentRow, $no);
                    $sheet->setCellValue("B" . $currentRow, $log->user->model->name);
                    $sheet->setCellValue("C" . $currentRow, $log->user->model->unit->name);
                    $sheet->setCellValue("D" . $currentRow, $log->user->model->position->name);
                    $sheet->setCellValue("E" . $currentRow, $log->created_at);
                    $sheet->setCellValue("F" . $currentRow, $log->longitude);
                    $sheet->setCellValue("G" . $currentRow, $log->latitude);
                    $sheet->setCellValue("H" . $currentRow, $log->address);

                    $currentRow++;
                }

                $styleArray = [
                    "borders" => [
                        "allBorders" => [
                            "borderStyle" =>
                                Border::BORDER_THIN,
                            "color" => ["argb" => "000000"],
                        ],
                    ],
                ];

                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                $cellRange = "A6:" . $highestColumn . $highestRow;

                $sheet
                    ->getDelegate()
                    ->getStyle($cellRange)
                    ->applyFromArray($styleArray);

                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);

                $sheet->setTitle('Pemasaran Dana');
            },
        ];
    }

    public function startCell(): string
    {
        return 'A6';
    }
}
