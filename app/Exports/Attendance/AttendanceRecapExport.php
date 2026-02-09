<?php
namespace App\Exports\Attendance;

ini_set('memory_limit', '-1');

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Str;

class AttendanceRecapExport implements FromCollection, WithCustomStartCell, WithColumnWidths, WithEvents
{
    protected array $data;

    public function __construct($data)
    {
        $this->data  = $data;
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

                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                $sheet->mergeCells("A2:" . $highestColumn . "2");
                $sheet->mergeCells("A3:" . $highestColumn . "3");
                $sheet->mergeCells("A4:" . $highestColumn . "4");

                $sheet->getStyle("A2:" . $highestColumn . "4")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->setCellValue("A2", Str::upper($this->data['headerTitle']));
                $sheet->setCellValue("A3", Str::upper($this->data['headerSubtitle']));
                $sheet->setCellValue("A4", Str::upper($this->data['additional_title']));

                $sheet->getRowDimension(6)->setRowHeight(30);
                $sheet->getRowDimension(7)->setRowHeight(30);

                $sheet->mergeCells("A6:A7");
                $sheet->mergeCells("B6:B7");
                $sheet->mergeCells("C6:C7");
                $sheet->mergeCells("D6:D7");
                $sheet->mergeCells("E6:E7");
                $sheet->mergeCells("F6:F7");
                $sheet->mergeCells("G6:G7");
                $sheet->mergeCells("H6:H7");
                $sheet->mergeCells("I6:I7");
                $sheet->mergeCells("J6:J7");
                $sheet->mergeCells("K6:K7");

                $sheet->setCellValue("A6", "No");
                $sheet->setCellValue("B6", "Nama Pegawai");
                $sheet->setCellValue("C6", "Jabatan");
                $sheet->setCellValue("D6", "HK");
                $sheet->setCellValue("E6", "HDR");
                $sheet->setCellValue("F6", "A");
                $sheet->setCellValue("G6", "I");
                $sheet->setCellValue("H6", "C");
                $sheet->setCellValue("I6", "S");
                $sheet->setCellValue("J6", "DL");
                $sheet->setCellValue("K6", "TD");

                $sheet->getDelegate()->getStyle('C8:C'.$highestRow)->getAlignment()->setWrapText(true);

                $styleArray = [
                    "borders" => [
                        "allBorders" => [
                            "borderStyle" =>
                                Border::BORDER_THIN,
                            "color" => ["argb" => "000000"],
                        ],
                    ],
                ];

                $cellRange = "A6:" . $highestColumn.$highestRow; // All headers
                $sheet
                    ->getDelegate()
                    ->getStyle($cellRange)
                    ->applyFromArray($styleArray);
                $sheet->getDelegate()->getStyle("A6:".$highestColumn."7")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getDelegate()->getStyle("A6:".$highestColumn."7")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getDelegate()->getStyle("A6:".$highestColumn."7")->getAlignment()->setWrapText(true);
            },
        ];
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 40,
            'C' => 40,
            'D' => 10,
            'E' => 10,
            'F' => 10,
            'G' => 10,
            'H' => 10,
            'I' => 10,
            'J' => 10,
            'K' => 10,
        ];
    }
}
