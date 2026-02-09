<?php

namespace App\Exports\PSA;

use App\Models\PSA\NplSetting;
use App\Models\PSA\ProductMonthlySetting;
use App\Models\PSA\SettingBoom;
use App\Models\PSA\SptScore;
use App\Services\PSA\AgentBoosterService;
use App\Services\PSA\ProductService;
use App\Services\PSA\SettingPositionProductService;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ReportIndividualSPTSingleExport implements WithCustomStartCell, WithEvents
{
    protected array $data;
    private SettingPositionProductService $settingPositionProductService;
    private ProductService $productService;
    private AgentBoosterService $agentBoosterService;

    function __construct($data)
    {
        $this->data = $data;
        $this->settingPositionProductService = app(SettingPositionProductService::class);
        $this->productService = app(ProductService::class);
        $this->agentBoosterService = app(AgentBoosterService::class);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $sheet->getDelegate()->setShowGridlines(false);

                $agent = $this->data['agent'];

                $isBoomPoint = false;

                /** PRODUCT */
                $settingProduct = $this->data['settingProducts']
                    ->filter(function ($item) use ($agent) {
                        return in_array($agent->position_id, explode(',', $item->positions))
                            && $item->type === 'p';
                    })
                    ->first();

                $settingProducts = $settingProduct?->details;
                $positionProducts = $settingProducts?->pluck('product_id');
                $settingProductForScore = $settingProduct;
                if ($settingProduct != 'Penghimpunan Dana Pihak Ketiga') {
                    $isBoomPoint = true;
                }

                $products = $this->data['products']
                    ->where('type', 'p')
                    ->whereIn('id', $positionProducts);

                $sheet->getDelegate()->getStyle('A1:G' . (13 + $products->count()))->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                ]);

                /** BOOSTERS */
                $settingBoosters = $this->settingPositionProductService->getSettingBoostersByPosition($agent->position_id);
                $positionBoosters = $this->settingPositionProductService->getBoostersByPosition($agent->position_id);
                $boosters = $this->data['products']
                    ->where('type', 'b')
                    ->whereIn('id', $positionBoosters);

                $startingRow = 15 + $products->count();

                // Buat objek Drawing
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo Perusahaan');
                $drawing->setPath(public_path('assets/media/logos/report-logo.png'));
                $drawing->setHeight(50); // Atur tinggi gambar sesuai kebutuhan
                $drawing->setCoordinates('A1'); // Menempatkan gambar di sel A1
                $drawing->setWorksheet($sheet->getDelegate()); // <- ini yang penting

                $sheet->setCellValue("C4", "Individual SPT");
                $sheet->setCellValue("C7", "Nama");
                $sheet->setCellValue("C8", "Jabatan");
                $sheet->setCellValue("C9", "Cabang/Unit");
                $sheet->setCellValue("C10", "Direct SPV");
                $sheet->setCellValue("C11", "Jabatan");

                $sheet->setCellValue("E7", $agent->name);
                $sheet->setCellValue("E8", $agent->position->name);
                $sheet->setCellValue("E9", $agent->unit->name);
                $sheet->setCellValue("E10", $agent?->leader?->name);
                $sheet->setCellValue("E11", $agent?->leader?->position?->name);

                $sheet->setCellValue("C13", "No");
                $sheet->setCellValue("D13", "Target");
                $sheet->setCellValue("E13", "Yearly");
                $sheet->setCellValue("F13", "Monthly");
                $sheet->setCellValue("G13", "Weight");

                $currentRow = 14;

                $no = 0;
                foreach ($products as $product) {
                    $no++;
                    $productSetting = $settingProducts->where('product_id', $product->id)->first();
                    $targetProduct = $this->data['targets']->where('product_id', $product->id)->first();
                    $monthlyTarget = $targetProduct?->total > 0 ? $targetProduct->total / 12 : 0;
                    $sheet->setCellValue("C" . $currentRow, $no);
                    $sheet->setCellValue("D" . $currentRow, $product->name);
                    $sheet->setCellValue("E" . $currentRow, setCurrency($targetProduct?->total ?? 0));
                    $sheet->setCellValue("F" . $currentRow, setCurrency($monthlyTarget));
                    $sheet->setCellValue("G" . $currentRow, ($productSetting->weight ?? 0) . "%");
                    $currentRow++;
                }

                $currentRow++;
                $currentRowPlus1 = $currentRow + 1;

                $cellRange = "C13:G" . $currentRow - 2;

                $styleArray = [
                    "borders" => [
                        "allBorders" => [
                            "borderStyle" =>
                                Border::BORDER_THIN,
                            "color" => ["argb" => "000000"],
                        ],
                    ],
                ];

                $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);

                $sheet->getRowDimension($currentRow)->setRowHeight(30);
                $sheet->getRowDimension($currentRowPlus1)->setRowHeight(30);

                $sheet->mergeCells("A1:B1");

                $sheet->mergeCells("C" . $currentRow . ":C" . $currentRowPlus1);
                $sheet->mergeCells("D" . $currentRow . ":D" . $currentRowPlus1);
                $sheet->mergeCells("E" . $currentRow . ":E" . $currentRowPlus1);
                $sheet->mergeCells("F" . $currentRow . ":F" . $currentRowPlus1);
                $sheet->mergeCells("G" . $currentRow . ":G" . $currentRowPlus1);
                $sheet->mergeCells("H" . $currentRow . ":H" . $currentRowPlus1);
                $sheet->mergeCells("I" . $currentRow . ":I" . $currentRowPlus1);
                $sheet->mergeCells("J" . $currentRow . ":J" . $currentRowPlus1);
                $sheet->mergeCells("K" . $currentRow . ":K" . $currentRowPlus1);

                $sheet->setCellValue("C" . $currentRow, 'NO');
                $sheet->setCellValue("D" . $currentRow, 'MONTH');
                $sheet->setCellValue("E" . $currentRow, 'ITEM PRODUCT');
                $sheet->setCellValue("F" . $currentRow, 'WEIGHT');
                $sheet->setCellValue("G" . $currentRow, 'TARGET SETTING');
                $sheet->setCellValue("H" . $currentRow, 'ACTUAL');
                $sheet->setCellValue("I" . $currentRow, 'ACHIEVEMENT VOLUME AMOUNT');
                $sheet->setCellValue("J" . $currentRow, 'PROSENTASE ACHIEVEMENT');
                $sheet->setCellValue("K" . $currentRow, 'SPT SCORE');

                $cellRange = "C" . $currentRow . ":K" . $currentRow;

                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);

                $sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);

                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(5);
                $sheet->getColumnDimension('C')->setWidth(10);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(20);
                $sheet->getColumnDimension('G')->setWidth(20);
                $sheet->getColumnDimension('H')->setWidth(20);
                $sheet->getColumnDimension('I')->setWidth(20);
                $sheet->getColumnDimension('J')->setWidth(20);
                $sheet->getColumnDimension('K')->setWidth(20);

                $agentBoosters = $this->data['agentBoosters']
                    ->where('agent_id', $agent->id);
                $booms = $this->data['booms'];

                $currentRow++;
                $year = date('Y');

                $totalAchievementByProducts = [];
                for ($i = 1; $i <= 12; $i++) {
                    $padMonth = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $startDate = $year . "-" . $padMonth . "-01";
                    $endDate = $year . "-" . $padMonth . "-31";

                    $currentRow++;
                    $sheet->setCellValue("C" . $currentRow, $i);
                    $sheet->setCellValue("D" . $currentRow, numToMonth($i));

                    $currentRowAfterProducts = $currentRow + $products->count() + $boosters->count() + 1 + ($isBoomPoint ? 1 : 0);


                    $sheet->mergeCells("C" . $currentRow . ":C" . $currentRowAfterProducts);
                    $sheet->mergeCells("D" . $currentRow . ":D" . $currentRowAfterProducts);
                    $sheet->mergeCells("K" . $currentRow . ":K" . $currentRowAfterProducts);

                    $totalAmount = 0;
                    $totalWeight = 0;
                    $totalTarget = 0;
                    $totalAchievement = 0;
                    $totalPercentage = 0;
                    foreach ($products as $product) {
                        $totalAmountProduct = 0;
                        $target = $this->data['targets']->where('product_id', $product->id)->first();
                        $selectedMonthTarget = $target?->details->where('month', $i)->first();
                        $selectedMonthActivities = $this->data['activities']->where('product_id', $product->id)
                            ->where('created_at', '>=', $startDate)
                            ->where('created_at', '<=', $endDate);
                        foreach ($selectedMonthActivities as $selectedMonthActivity) {
                            foreach ($selectedMonthActivity->details as $selectedMonthActivityDetail) {
                                $totalAmountProduct += $selectedMonthActivityDetail->closing_amount;
                            }
                        }

                        if (!isset($totalAchievementByProducts[$product->id])) {
                            $totalAchievementByProducts[$product->id] = 0;
                        }

                        $totalAchievementByProducts[$product->id] += $totalAmountProduct;

                        $percentage = ($totalAmountProduct > 0 && $selectedMonthTarget?->amount > 0 ? $totalAmountProduct / $selectedMonthTarget->amount * 100 : 0) * $settingProduct?->weight / 100;

                        $percentageText = decimalFormat($percentage) . "%";

                        $totalPercentage += $percentage;

                        $settingProduct = $settingProducts->where('product_id', $product->id)->first();

                        $sheet->setCellValue("E" . $currentRow, $product->name);
                        $sheet->setCellValue("F" . $currentRow, ($settingProduct?->weight ?? 0) . " %");
                        $sheet->setCellValue("G" . $currentRow, setCurrency($selectedMonthTarget?->amount ?? 0));
                        $sheet->setCellValue("H" . $currentRow, setCurrency($totalAmountProduct));
                        $sheet->setCellValue("I" . $currentRow, setCurrency($totalAchievementByProducts[$product->id] ?? 0));
                        $sheet->setCellValue("J" . $currentRow, $percentageText);
                        $currentRow++;

                        $totalAmount += $totalAmountProduct;
                        $totalAchievement += $totalAchievementByProducts[$product->id] ?? 0;
                        $totalWeight += $settingProduct?->weight ?? 0;
                        $totalTarget += $selectedMonthTarget?->amount ?? 0;
                    }

                    $sheet->getDelegate()->getStyle('E' . $currentRow . ':J' . $currentRow)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                        ],
                    ]);

                    $rowTotalProducts = $currentRow;


                    $currentRow++;

                    $score = $this->getScore($totalPercentage, $settingProductForScore?->id ?? 0);

                    $totalAmountBooster = 0;
                    $totalWeightBooster = 0;
                    $totalTargetBooster = 0;
                    $totalAchievementBooster = 0;
                    $totalPercentageBooster = 0;

                    foreach ($boosters as $booster) {
                        $amountBooster = $agentBoosters->where('product_id', $booster->id)
                            ->where('month', $i)
                            ->first()?->value ?? 0;
                        if ($booster->name == 'NPL' || $booster->name == 'Penurunan NPL Komersial/Konsumer') {
                            $amountBooster = $this->data['booms']->where('agent_id', $agent->id)->where('month', $i)->first()?->value ?? 0;
                        }
                        $target = $this->data['targets']->where('product_id', $booster->id)->first();
                        $selectedMonthTarget = $target?->details->where('month', $i)->first();

                        $percentage = $amountBooster > 0 && $selectedMonthTarget?->amount > 0 ? $amountBooster / $selectedMonthTarget->amount * 100 : 0;

                        if ($booster->name == 'NPL' || $booster->name == 'Penurunan NPL Komersial/Konsumer') {
                            $percentage = $amountBooster > 0 && $selectedMonthTarget?->amount > 0 ? (1 + (1 - ($amountBooster / $selectedMonthTarget->amount))) * 100 : 0;
                        }

                        $totalPercentageBooster += $percentage;

                        $settingBooster = $settingBoosters->where('product_id', $booster->id)->first();

                        $sheet->setCellValue("E" . $currentRow, $booster->name);
                        $sheet->setCellValue("F" . $currentRow, ($settingBooster?->weight ?? 0) . " %");
                        $sheet->setCellValue("G" . $currentRow, setCurrency($selectedMonthTarget?->amount ?? 0));
                        $sheet->setCellValue("H" . $currentRow, $amountBooster);
                        $sheet->setCellValue("I" . $currentRow, $amountBooster);
                        $sheet->setCellValue("J" . $currentRow, decimalFormat($percentage) . "%");
                        $currentRow++;

                        $totalAmountBooster += $amountBooster;
                        $totalAchievementBooster += $amountBooster;
                        $totalWeightBooster += $settingBooster?->weight ?? 0;
                        $totalTargetBooster += $selectedMonthTarget?->amount ?? 0;
                    }

                    $sheet->getDelegate()->getStyle('E' . $currentRow . ':J' . $currentRow)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                        ],
                    ]);

                    $sheet->setCellValue("E" . $currentRow, "Total");
                    $sheet->setCellValue("F" . $currentRow, $totalWeightBooster . "%");
                    $sheet->setCellValue("G" . $currentRow, setCurrency($totalTargetBooster));
                    $sheet->setCellValue("H" . $currentRow, setCurrency($totalAmountBooster));
                    $sheet->setCellValue("I" . $currentRow, setCurrency($totalAchievementBooster));
                    $sheet->setCellValue("J" . $currentRow, decimalFormat($totalPercentageBooster) . "%");
                    $sheet->setCellValue("K" . $currentRow - $products->count() - $boosters->count() - 1, $score);

                    $currentRow++;

                    $deltaBoom = 0;
                    if ($isBoomPoint) {
                        $boomPoint = $booms->where('agent_id', $agent->id)
                            ->where('month', $i)
                            ->first();
                        $sheet->setCellValue("E" . $currentRow, "NPL");
                        $sheet->setCellValue("F" . $currentRow, "Max -7%");
                        $sheet->setCellValue("G" . $currentRow, $boomPoint->target ?? 0);
                        $sheet->setCellValue("H" . $currentRow, $boomPoint->realization ?? 0);
                        $sheet->setCellValue("I" . $currentRow, $boomPoint->realization ?? 0);
                        $sheet->setCellValue("J" . $currentRow, ($boomPoint->realization ?? 0)."%");

                        if($boomPoint?->target < $boomPoint?->realization) {
                            $deltaBoom = 7;
                        }
                    }

                    $totalPercentage = $totalPercentage - $deltaBoom;

                    $totalPercentageText =
                        $totalPercentage > 100 ? '> 100 %' :
                            decimalFormat($totalPercentage- $deltaBoom) . "%";

                    $sheet->setCellValue("E" . $rowTotalProducts, "Total");
                    $sheet->setCellValue("F" . $rowTotalProducts, $totalWeight . "%");
                    $sheet->setCellValue("G" . $rowTotalProducts, setCurrency($totalTarget));
                    $sheet->setCellValue("H" . $rowTotalProducts, setCurrency($totalAmount));
                    $sheet->setCellValue("I" . $rowTotalProducts, setCurrency($totalAchievement));
                    $sheet->setCellValue("J" . $rowTotalProducts, $totalPercentageText);
                }

                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                $cellRange = "C" . $startingRow . ":" . $highestColumn . $highestRow;

                $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);

                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);

                $cellRange = "E" . ($startingRow + 2) . ":E" . $highestRow;
                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $cellRange = "F" . ($startingRow + 2) . ":J" . $highestRow;
                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->setTitle(\Str::substr($this->data['agent']->name, 0, 30));
            },
        ];
    }

    public function startCell(): string
    {
        $startingRow = 17 + $this->data['products']->count();

        return 'C' . $startingRow;
    }

    private function getScore($value, $settingProductId)
    {
        $scores = $this->data['scores']->where('setting_id', $settingProductId);
        foreach ($scores as $score) {
            if (empty($score->minimum_value)) {
                if ($value >= $score->maximum_value) {
                    return $score->score;
                }
            } else if (empty($score->maximum_value)) {
                if ($value < $score->minimum_value) {
                    return $score->score;
                }
            } else {
                if ($value >= $score->minimum_value && $value < $score->maximum_value) {
                    return $score->score;
                }
            }
        }

        return 0;
    }
}
