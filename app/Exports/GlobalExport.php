<?php
namespace App\Exports;

ini_set('memory_limit', '-1');

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Str;

class GlobalExport implements FromCollection, WithCustomStartCell, WithEvents
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
                $arrAligns = [
                    'center' => Alignment::HORIZONTAL_CENTER,
                    'left' => Alignment::HORIZONTAL_LEFT,
                    'right' => Alignment::HORIZONTAL_RIGHT,
                ];

                $columns = $this->data['columns'];
                $data = $this->data['data'];
                $startRow = $this->data['startRow'] ?? 5;
                $startColumn = $this->data['startColumn'] ?? 'A';
                $title = $this->data['title'];
                $subtitle = $this->data['subtitle'] ?? '';
                $widths = $this->data['widths'];
                $aligns = $this->data['aligns'];

                $sheet = $event->sheet;

                $sheet->setCellValue("A2", Str::upper($title));
                $sheet->setCellValue("A3", Str::upper($subtitle));

                $sheet->getRowDimension($startRow)->setRowHeight(30);
                $sheet->getRowDimension($startRow + 1)->setRowHeight(30);

                $column = 0;
                foreach ($columns as $index => $value){
                    if(is_array($value)) {
                        $lastColumnArray = $column + count($value) - 1;
                        $sheet->setCellValue(numToAlpha($column)."5", Str::upper($index));
                        $sheet->mergeCells(numToAlpha($column)."5:".numToAlpha($lastColumnArray)."5");
                        foreach ($value as $key => $val){
                            $sheet->setCellValue(numToAlpha($column)."6", Str::upper($val));
                            $width = $widths[$column] ?? 20;
                            $sheet->getColumnDimension(numToAlpha($column))->setWidth($width);
                            $column++;
                        }
                    }else{
                        $sheet->mergeCells(numToAlpha($column)."5:".numToAlpha($column)."6");
                        $sheet->setCellValue(numToAlpha($column)."5", Str::upper($value));
                        $width = $widths[$column] ?? 20;
                        $sheet->getColumnDimension(numToAlpha($column))->setWidth($width);
                        $column++;
                    }
                }

                $column--;

                $highestRow = count($data) + 6;

                $sheet->mergeCells("A2:".numToAlpha($column)."2");
                $sheet->mergeCells("A3:".numToAlpha($column)."3");

                $sheet->getStyle("A2:".numToAlpha($column)."3")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                if(isset($aligns)) {
                    foreach ($aligns as $key => $align) {
                        $sheet->getStyle(numToAlpha($key) . ($startRow + 2).":" . numToAlpha($key) . $highestRow)->applyFromArray([
                            'alignment' => [
                                'horizontal' => $arrAligns[$align],
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);
                    }
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

                $cellRange = $startColumn.$startRow.":" .numToAlpha($column).$highestRow; // All headers
                $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $sheet->getDelegate()->getStyle($startColumn.$startRow.":".numToAlpha($column).$startRow+1)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
                $sheet->getDelegate()->getStyle($startColumn.$startRow.":".numToAlpha($column).$startRow+1)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getDelegate()->getStyle($startColumn.$startRow.":".numToAlpha($column).$startRow+1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getDelegate()->getStyle($startColumn.$startRow.":".numToAlpha($column).$startRow+1)->getAlignment()->setWrapText(true);
                $sheet->setTitle($title);

            },
        ];
    }

    public function startCell(): string
    {
        $startColumn = $this->data['startColumn'] ?? 'A';
        $startRow = $this->data['startRow'] ?? 5;
        return $startColumn . ($startRow + 2);
    }
}
