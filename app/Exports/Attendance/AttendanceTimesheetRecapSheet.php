<?php
namespace App\Exports\Attendance;

ini_set('memory_limit', '-1');

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Str;

class AttendanceTimesheetRecapSheet implements FromCollection, WithCustomStartCell, WithColumnWidths, WithEvents, WithTitle
{
    protected array $data;
    private string $title;

    public function __construct($title, $data)
    {
        $this->data  = $data;
        $this->title = $title;
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

                $totalColumn = $this->data['totalDays'] + 2;

                $sheet->setCellValue("A2", Str::upper($this->data['headerTitle']));
                $sheet->setCellValue("A3", Str::upper($this->data['headerSubtitle']));

                //set height for row 5 and 6
                $sheet->getRowDimension(5)->setRowHeight(30);
                $sheet->getRowDimension(6)->setRowHeight(30);

                $sheet->mergeCells("A5:A6");
                $sheet->mergeCells("B5:B6");
                $sheet->mergeCells("C5:C6");

                $sheet->mergeCells('D5:'.numToAlpha($totalColumn).'5');

                $sheet->setCellValue("A5", "No");
                $sheet->setCellValue("B5", "NIP");
                $sheet->setCellValue("C5", "Nama");
                $sheet->setCellValue("D5", "TANGGAL");

                for ($i = 1; $i <= $this->data['totalDays']; $i++) $sheet->setCellValue(numToAlpha($i + 2)."6", $i);
                $sheet->mergeCells(numToAlpha($i + 2).'5:'.numToAlpha($i + 2).'6');
                $sheet->setCellValue(numToAlpha($i + 2)."5", "TOTAL");

                $event->sheet->getDelegate()->getStyle("B6:B".$highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle("D6:".$highestColumn.$highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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

            },
        ];
    }

    public function startCell(): string
    {
        return 'A7';
    }

    public function columnWidths(): array
    {
        $cols = 3;
        $arr = array("A" => 5, "B" => "20", "C" => "40");
        for ($i = 1; $i<= $this->data['totalDays']; $i++){
            $arr[numToAlpha($cols)] = 10;
            $cols++;
        }
        $arr[numToAlpha($cols)] = 10;

        return $arr;
    }

    public function title(): string
    {
        return $this->title;
    }
}
