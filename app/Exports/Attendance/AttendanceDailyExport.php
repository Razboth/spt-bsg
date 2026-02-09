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

class AttendanceDailyExport implements FromCollection, WithCustomStartCell, WithColumnWidths, WithEvents
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

                $highestColumn = $sheet->getHighestColumn();

                $sheet->mergeCells("A2:".$highestColumn."2");
                $sheet->mergeCells("A3:".$highestColumn."3");

                $sheet->getStyle("A2:".$highestColumn."3")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->setCellValue("A2", Str::upper($this->data['title']));
                $sheet->setCellValue("A3", Str::upper($this->data['subtitle']));

                //set height for row 5 and 6
                $sheet->getRowDimension(5)->setRowHeight(30);
                $sheet->getRowDimension(6)->setRowHeight(30);

                $sheet->mergeCells("A5:A6");
                $sheet->mergeCells("B5:B6");
                $sheet->mergeCells("C5:C6");
                $sheet->mergeCells("D5:D6");

                $sheet->mergeCells("I5:I6");
                $sheet->mergeCells("J5:J6");
                $sheet->mergeCells("K5:K6");

                $sheet->mergeCells("E5:F5");
                $sheet->mergeCells("G5:H5");

                $sheet->setCellValue("A5", "No");
                $sheet->setCellValue("B5", "NIP");
                $sheet->setCellValue("C5", "Nama");
                $sheet->setCellValue("D5", "TANGGAL");
                $sheet->setCellValue("E5", "JADWAL");
                $sheet->setCellValue("G5", "AKTUAL");
                $sheet->setCellValue("I5", "DURASI");
                $sheet->setCellValue("J5", "KEHADIRAN");
                $sheet->setCellValue("K5", "KETERANGAN");

                $sheet->setCellValue("E6", "MASUK");
                $sheet->setCellValue("F6", "PULANG");
                $sheet->setCellValue("G6", "MASUK");
                $sheet->setCellValue("H6", "PULANG");

                $styleArray = [
                    "borders" => [
                        "allBorders" => [
                            "borderStyle" =>
                                Border::BORDER_THIN,
                            "color" => ["argb" => "000000"],
                        ],
                    ],
                ];

                $cellRange = "A5:" .$highestColumn.$sheet->getHighestRow(); // All headers
                $event->sheet
                    ->getDelegate()
                    ->getStyle($cellRange)
                    ->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle("A5:".$highestColumn."6")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $event->sheet->getDelegate()->getStyle("A5:".$highestColumn."6")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle("A5:".$highestColumn."6")->getAlignment()->setWrapText(true);
                $sheet->setTitle($this->data['title']);

            },
        ];
    }

    public function startCell(): string
    {
        return 'A7';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 20,
            'C' => 40,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 15,
            'K' => 30,
        ];
    }
}
