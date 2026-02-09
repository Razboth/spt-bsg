<?php

namespace App\Exports\PSA;

use App\Models\PSA\Product;
use App\Models\PSA\ProductMonthlySetting;
use App\Models\PSA\TargetAgentDetail;
use App\Services\PSA\ActivityService;
use App\Services\PSA\AgentService;
use App\Services\PSA\SettingPositionProductService;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportIncentiveRecipientExport implements WithEvents
{
    protected array $data;

    private ActivityService $activityService;
    private AgentService $agentService;
    private SettingPositionProductService $settingPositionProductService;

    function __construct($data)
    {
        $this->data = $data;
        $this->activityService = new ActivityService();
        $this->agentService = new AgentService();
        $this->settingPositionProductService = new SettingPositionProductService();
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

                $settingPosition = $this->settingPositionProductService->getSettingPositionProductById($this->data['settingPositionId']);

                $sheet->setCellValue("A1", $this->data['title']);
                $sheet->setCellValue("A2", 'SPT BSG - ' . $settingPosition->name);

                $sheet->getRowDimension(4)->setRowHeight(30);
                $sheet->getRowDimension(5)->setRowHeight(30);

                $sheet->mergeCells("A1:B1");

                $sheet->mergeCells("A4:A5");
                $sheet->mergeCells("B4:B5");
                $sheet->mergeCells("C4:C5");
                $sheet->mergeCells("D4:D5");
                $sheet->mergeCells("E4:E5");
                $sheet->mergeCells("F4:F5");

                $sheet->setCellValue("A4", 'NO');
                $sheet->setCellValue("B4", 'NAMA');
                $sheet->setCellValue("C4", 'CABANG');
                $sheet->setCellValue("D4", 'JABATAN');
                $sheet->setCellValue("E4", 'STATUS');
                $sheet->setCellValue("F4", 'BULAN');

                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(40);
                $sheet->getColumnDimension('C')->setWidth(30);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(20);

                $settingProduct = $this->settingPositionProductService->getSettingPositionProductById($this->data['settingPositionId']);
                $positionProducts = $settingProduct->details->pluck('product_id');
                $products = Product::whereIn('id', $positionProducts)
                    ->where('type', 'p')
                    ->get();

                $startColumn = 6;
                foreach ($products as $product) {
                    $sheet->setCellValue(numToAlpha($startColumn) . "4", $product->name);
                    $mergeEnd = numToAlpha($startColumn + 3);
                    $sheet->mergeCells(numToAlpha($startColumn) . "4:" . $mergeEnd . "4");

                    $sheet->setCellValue(numToAlpha($startColumn) . "5", "Realisasi");
                    $sheet->getColumnDimension(numToAlpha($startColumn))->setWidth(20);

                    $startColumn++;
                    $sheet->setCellValue(numToAlpha($startColumn) . "5", "Based Monthly Target");
                    $sheet->getColumnDimension(numToAlpha($startColumn))->setWidth(20);

                    $startColumn++;
                    $sheet->setCellValue(numToAlpha($startColumn) . "5", "%");
                    $sheet->getColumnDimension(numToAlpha($startColumn))->setWidth(5);

                    $startColumn++;
                    $sheet->setCellValue(numToAlpha($startColumn) . "5", "Nominal");
                    $sheet->getColumnDimension(numToAlpha($startColumn))->setWidth(20);

                    $startColumn++;
                }

                $sheet->mergeCells(numToAlpha($startColumn) . "4:" . numToAlpha($startColumn) . "5");
                $sheet->setCellValue(numToAlpha($startColumn) . "4", "Total Insentif");
                $sheet->getColumnDimension(numToAlpha($startColumn))->setWidth(20);

                $startColumn++;
                $sheet->mergeCells(numToAlpha($startColumn) . "4:" . numToAlpha($startColumn) . "5");
                $sheet->setCellValue(numToAlpha($startColumn) . "4", "Payment 70%");
                $sheet->getColumnDimension(numToAlpha($startColumn))->setWidth(20);

                $startColumn++;
                $sheet->mergeCells(numToAlpha($startColumn) . "4:" . numToAlpha($startColumn) . "5");
                $sheet->setCellValue(numToAlpha($startColumn) . "4", "Hold 30% to Dec");
                $sheet->getColumnDimension(numToAlpha($startColumn))->setWidth(20);

                $startColumn++;
                $sheet->mergeCells(numToAlpha($startColumn) . "4:" . numToAlpha($startColumn) . "5");
                $sheet->setCellValue(numToAlpha($startColumn) . "4", "Total Insentif Dec");
                $sheet->getColumnDimension(numToAlpha($startColumn))->setWidth(20);

                $startDate = resetDate($this->data['startDate']);
                $endDate = resetDate($this->data['endDate']);

                list($yearStart, $monthStart, $dayStart) = explode("-", $startDate);
                list($yearEnd, $monthEnd, $dayEnd) = explode("-", $endDate);

                //SET ROW
                $startContentRow = 6;
                /** PRODUCT */
                $settingPosition = $settingPosition->positions;
                $settingPositions = explode(',', $settingPosition);
                $agents = $this->agentService->getAgent(isSalesOnly: true)
                    ->whereIn('position_id', $settingPositions)
                    ->when($this->data['unitId'] != null, function ($query) {
                        $query->where('unit_id', $this->data['unitId']);
                    })
                    ->when($this->data['filter'] != null, function ($query) {
                        $query->where('name', 'like', '%' . $this->data['filter'] . '%');
                    })
                    ->when(\Auth::user()->role->name == 'Manager', function ($query) {
                        $query->where('leader_id', \Auth::user()->model_id);
                    })
                    ->get();
                $no = 0;
                foreach ($agents as $agent) {
                    $totalDec = 0;
                    for ($i = $monthStart; $i <= $monthEnd; $i++) {
                        $arrData = $this->getDataIncentive($i, $yearStart);
                        $no++;
                        $sheet->setCellValue("A" . $startContentRow, $no);
                        $sheet->setCellValue("B" . $startContentRow, $agent->name);
                        $sheet->setCellValue("C" . $startContentRow, $agent->unit->name);
                        $sheet->setCellValue("D" . $startContentRow, $agent->position->name);
                        $sheet->setCellValue("E" . $startContentRow, 'Eligible');
                        $sheet->setCellValue("F" . $startContentRow, numToMonth($i));

                        $totalNominal = 0;

                        $dynamicColumn = 6;
                        foreach ($products as $product) {
                            $realization = $arrData['realization'][$agent->id][$product->id] ?? 0;
                            $target = $arrData['target'][$agent->id][$product->id] ?? 0;

                            // Hitung jumlah yang sesuai (min dari realization dan target)
                            $amount = min($realization, $target);

                            $productWeight = ProductMonthlySetting::where('product_id', $product->id)
                                ->where('month', $i)
                                ->where('year', $yearStart)
                                ->first();

                            $productWeightValue = $productWeight->weight ?? 0.135;

                            $nominal = $amount * $productWeightValue;
                            $totalNominal += $nominal;

                            $sheet->setCellValue(numToAlpha($dynamicColumn) . $startContentRow, setCurrency($realization ?? 0));
                            $dynamicColumn++;
                            $sheet->setCellValue(numToAlpha($dynamicColumn) . $startContentRow, setCurrency($amount ?? 0));
                            $dynamicColumn++;
                            $sheet->setCellValue(numToAlpha($dynamicColumn) . $startContentRow, $productWeightValue . ' %');
                            $dynamicColumn++;
                            $sheet->setCellValue(numToAlpha($dynamicColumn) . $startContentRow, setCurrency($nominal));
                            $dynamicColumn++;
                        }

                        $incentive70 = $totalNominal * 0.7;
                        $incentive30 = $totalNominal * 0.3;

                        $totalDec += $incentive30;

                        $sheet->setCellValue(numToAlpha($dynamicColumn) . $startContentRow, setCurrency($totalNominal ?? 0));
                        $dynamicColumn++;
                        $sheet->setCellValue(numToAlpha($dynamicColumn) . $startContentRow, setCurrency($incentive70));
                        $dynamicColumn++;
                        $sheet->setCellValue(numToAlpha($dynamicColumn) . $startContentRow, setCurrency($incentive30));
                        $dynamicColumn++;
                        $sheet->setCellValue(numToAlpha($dynamicColumn) . $startContentRow, setCurrency($i == 12 ? $totalDec ?? 0 : 0));

                        $startContentRow++;
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

                $cellRange = "A4:" . numToAlpha($startColumn) . "5";
                $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);

                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                $cellRange = "A4:" . $highestColumn . $highestRow;

                $sheet
                    ->getDelegate()
                    ->getStyle($cellRange)
                    ->applyFromArray($styleArray);

                $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);

                $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);

                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);

                $cellRange = "G6:" . $highestColumn . $highestRow;

                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);

                $sheet->setTitle('Pemasaran Dana');
            },];
    }

    public function getDataIncentive($month, $year)
    {
        $activities = $this->activityService->getActivities()
            ->whereHas('details', function ($query) use ($month, $year) {
                $query->whereMonth('realization_date', $month)
                    ->whereYear('realization_date', $year);
            })
            ->get();
        $arrData = [];

        foreach ($activities as $activity) {
            $activity->details
                ->each(function ($detail) use (&$arrData, $activity) {
                    if (!isset($arrData['realization'][$activity->agent_id][$activity->product_id])) {
                        $arrData['realization'][$activity->agent_id][$activity->product_id] = 0;
                    }
                    $arrData['realization'][$activity->agent_id][$activity->product_id] += $detail->closing_amount;
                });
        }

        $targets = TargetAgentDetail::where('month', $month)
            ->where('year', $year)
            ->get();
        foreach ($targets as $target) {
            $arrData['target'][$target->targetAgent->agent_id][$target->targetAgent->product_id] = $target->amount;
        }

        return $arrData;
    }
}
