<?php

namespace App\Imports\PSA;

use App\Models\PSA\Agent;
use App\Models\PSA\Product;
use App\Models\PSA\TargetAgent;
use App\Models\PSA\TargetAgentDetail;
use App\Models\PSA\TargetPeriod;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;

class SalesTargetImport implements ToModel, WithEvents
{
    public string $logname;
    public array $agents;
    public array $products;

    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "sales-target-import-$today.log";

        $this->products = Product::select([\DB::raw('lower(name) as name'), 'id'])
            ->where('type', 'p')
            ->pluck('id', 'name')
            ->toArray();
        $this->agents = Agent::select([\DB::raw('REPLACE(lower(employee_number), \'.\', \'\') as employee_number'), 'id'])->pluck('id', 'employee_number')->toArray();
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function () {
                $storage = Storage::disk('log');
                $storage->put($this->logname, '');
                $now = now()->format("[Y-m-d H:i:s]");
                $storage->append($this->logname, "{$now} START");
            },
            AfterImport::class => function () {
                $storage = Storage::disk('log');
                $now = now()->format("[Y-m-d H:i:s]");
                $storage->append($this->logname, "{$now} FINISH");
            },
        ];
    }

    /**
     * @param array $row
     *
     * @return void|null
     */
    public function model(array $row)
    {
        if (!is_numeric($row[0])) {
            return null;
        }

        $storage = Storage::disk("log");

        $no = trim($row[0]);
        $code = trim($row[1]);
        $name = trim($row[2]);
        $productName = trim($row[5]);
        $unitTarget = trim($row[6]);
        $totalMonth = trim($row[7]);
        $weight = trim($row[8]);
        $value = trim($row[10]);

        $errors = "";
        try {
            //VALIDATION EMPTY
            if (empty($code)) $errors .= "\n\t-Kolom kode tidak boleh kosong";
            if (empty($name)) $errors .= "\n\t-Kolom nama tidak boleh kosong";
            if (empty($productName)) $errors .= "\n\t-Kolom produk tidak boleh kosong";
            if (empty($value)) $errors .= "\n\t-Kolom nilai tidak boleh kosong";

            //VALIDATION MASTER
            if (!array_key_exists(trim(strtolower($code)), $this->agents)) $errors .= "\n\t-Kolom sales tidak terdaftar " . $code;
            if (!array_key_exists(trim(strtolower($productName)), $this->products)) $errors .= "\n\t-Kolom produk tidak terdaftar " . $productName;

            $now = now()->format("[Y-m-d H:i:s]");
            if (!empty($errors)) {
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : " . $errors);
            } else {

                $weight = \Str::replace('%', '', $weight);
                $yearlyValue = (float)$unitTarget * (float)$weight / 100;
                $arrData = [
                    'agent_id' => $this->agents[trim(strtolower($code))],
                    'product_id' => $this->products[trim(strtolower($productName))],
                    'value' => $yearlyValue / $totalMonth,
                    'yearly_value' => $yearlyValue,
                ];

                $getPeriod = TargetPeriod::orderBy('end_date', 'desc')->first();

                $target = TargetAgent::updateOrCreate([
                    'type' => '1',
                    'target_period_id' => $getPeriod->id,
                    'product_id' => $arrData['product_id'],
                    'agent_id' => $arrData['agent_id'],
                ], [
                    'total' => $arrData['yearly_value'],
                ]);

                for ($i = 1; $i <= $totalMonth; $i++) {
                    TargetAgentDetail::updateOrCreate([
                        'target_agent_id' => $target->id,
                        'month' => $i,
                        'year' => explode('-', $getPeriod->end_date)[0]
                    ], [
                        'amount' => $arrData['value'],
                    ]);
                }

                $now = now()->format("[Y-m-d H:i:s]");
                $storage->append($this->logname, "{$now} SUCESS {$no} {$name}");
            }
        } catch (\Throwable $th) {
            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "{$now} ERROR {$no} {$name} {$th->getMessage()}");
        }
    }
}
