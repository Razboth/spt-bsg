<?php

namespace App\Exports\Setting;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MenuChecklistExport implements FromCollection, WithCustomStartCell, WithEvents
{
    protected array $data;

    function __construct($data) {
        $this->data = $data;
    }
    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return collect($this->data['data']);
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

                $sheet->setCellValue("A1", 'USER ACCEPTANCE TEST');
                $sheet->setCellValue("A2", 'MODUL : '.$this->data['module']);

                $sheet->getRowDimension(5)->setRowHeight(30);
                $sheet->getRowDimension(6)->setRowHeight(30);

                $sheet->mergeCells("A1:B1");

                $sheet->mergeCells("A5:A6");
                $sheet->mergeCells("B5:B6");
                $sheet->mergeCells("C5:F5");
                $sheet->mergeCells("G5:G6");

                $sheet->setCellValue("A5", 'NO');
                $sheet->setCellValue("B5", 'MENU');
                $sheet->setCellValue("C5", 'CHECKLIST');

                $sheet->setCellValue("C6", 'VIEW');
                $sheet->setCellValue("D6", 'INPUT');
                $sheet->setCellValue("E6", 'EDIT');
                $sheet->setCellValue("F6", 'DELETE');

                $sheet->setCellValue("G5", 'KETERANGAN');

                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(10);
                $sheet->getColumnDimension('D')->setWidth(10);
                $sheet->getColumnDimension('E')->setWidth(10);
                $sheet->getColumnDimension('F')->setWidth(10);
                $sheet->getColumnDimension('G')->setWidth(60);


                $styleArray = [
                    "borders" => [
                        "allBorders" => [
                            "borderStyle" =>
                                Border::BORDER_THIN,
                            "color" => ["argb" => "000000"],
                        ],
                    ],
                ];

                $lastRow = 6 + count($this->data['data']);

                $sheet->getStyle("A7:A". $lastRow)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $cellRange = "A5:G6";
                $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);

                $cellRange = "A7:G" . $lastRow; // All headers
                $sheet
                    ->getDelegate()
                    ->getStyle($cellRange)
                    ->applyFromArray($styleArray);

                //FOOTER START
                $footerRowStart = $lastRow + 2;

                $sheet->mergeCells("A".$footerRowStart.":E".$footerRowStart);
                $sheet->mergeCells("F".$footerRowStart.":G".$footerRowStart);

                $sheet->setCellValue("A".$footerRowStart, 'VENDOR');
                $sheet->setCellValue("F".$footerRowStart, 'CLIENT');

                $footerRowTtd = $footerRowStart + 1;
                $footerRowTtdUntil = $footerRowTtd + 5;

                $sheet->mergeCells("A".$footerRowTtd.":E".$footerRowTtdUntil);
                $sheet->mergeCells("F".$footerRowTtd.":G".$footerRowTtdUntil);

                $sheet->setCellValue("A".$footerRowTtd, '');
                $sheet->setCellValue("F".$footerRowTtd, '');

                $cellRange = "A".$footerRowStart.":G".$footerRowTtdUntil; // All headers
                $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);

                $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);

                $sheet->setTitle('USER ACCEPTANCE TEST');
            },
        ];
    }

    public function startCell(): string
    {
        return 'A7';
    }
}
