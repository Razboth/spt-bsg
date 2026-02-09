<?php
namespace App\Exports\Payroll;

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

class PayrollRecapSheet implements FromCollection, WithCustomStartCell, WithColumnWidths, WithEvents, WithTitle
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

                $sheet->mergeCells("A2:".$highestColumn."2");
                $sheet->mergeCells("A3:".$highestColumn."3");
                $sheet->mergeCells("A4:".$highestColumn."4");

                $sheet->getStyle("A2:".$highestColumn."4")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->setCellValue("A2", Str::upper($this->data['headerTitle']));
                $sheet->setCellValue("A3", Str::upper($this->data['headerSubtitle']));
                $sheet->setCellValue("A4", Str::upper($this->data['additional_title']));

                //set height for row 5 and 6
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
                $sheet->mergeCells("N6:N7");
                $sheet->mergeCells("O6:O7");
                $sheet->mergeCells("P6:P7");
                $sheet->mergeCells("S6:S7");

                $sheet->mergeCells("K6:M6");
                $sheet->mergeCells("Q6:R6");

                $sheet->setCellValue("A6", "No");
                $sheet->setCellValue("B6", "NIP");
                $sheet->setCellValue("C6", "Nama");
                $sheet->setCellValue("D6", "Golongan");
                $sheet->setCellValue("E6", "Kelas Jabatan");
                $sheet->setCellValue("F6", "Nilai");
                $sheet->setCellValue("G6", "Status");
                $sheet->setCellValue("H6", "PT");
                $sheet->setCellValue("I6", "PC");
                $sheet->setCellValue("J6", "TPTC");
                $sheet->setCellValue("K6", "HUKUMAN DISIPLIN");
                $sheet->setCellValue("N6", "SAKIT > 3 HARI TANPA KETERANGAN");
                $sheet->setCellValue("O6", "CUTI BESAR/DILUAR TANGGUNGAN NEGARA");
                $sheet->setCellValue("P6", "IZIN BELAJAR");
                $sheet->setCellValue("Q6", "TOTAL PEMOTONGAN");
                $sheet->setCellValue("S6", "JUMLAH DITERIMA");

                $sheet->setCellValue("K7", "RINGAN");
                $sheet->setCellValue("L7", "SEDANG");
                $sheet->setCellValue("M7", "BERAT");

                $sheet->setCellValue("Q7", "%");
                $sheet->setCellValue("R7", "Rp.");

                $styleArray = [
                    "borders" => [
                        "allBorders" => [
                            "borderStyle" =>
                                Border::BORDER_THIN,
                            "color" => ["argb" => "000000"],
                        ],
                    ],
                ];

                $cellRange = "A6:S" . $sheet->getHighestRow(); // All headers
                $sheet
                    ->getDelegate()
                    ->getStyle($cellRange)
                    ->applyFromArray($styleArray);
                $sheet->getDelegate()->getStyle("A6:S7")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getDelegate()->getStyle("A6:S7")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getDelegate()->getStyle("A6:S7")->getAlignment()->setWrapText(true);
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
            'C' => 20,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 10,
            'J' => 20,
            'K' => 15,
            'L' => 15,
            'M' => 15,
            'N' => 15,
            'O' => 15,
            'P' => 15,
            'Q' => 15,
            'R' => 15,
            'S' => 15,
        ];
    }

    public function title(): string
    {
        return $this->title;
    }
}
