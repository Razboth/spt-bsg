<?php

namespace App\Imports\PSA;

use App\Http\Requests\PSA\AgentBoosterRequest;
use App\Services\PSA\AgentBoosterService;
use App\Services\PSA\AgentService;
use App\Services\PSA\CustomerService;
use App\Services\PSA\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;

class BoosterImport implements ToModel, WithEvents
{
    public string $logname;
    private AgentService $agentService;
    private ProductService $productService;
    private AgentBoosterService $agentBoosterService;
    private CustomerService $customerService;
    public array $agents;
    public array $products;
    public array $customers;

    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "psychotest-import-$today.log";
        $this->agentService = new AgentService();
        $this->productService = new ProductService();
        $this->agentBoosterService = new AgentBoosterService();
        $this->customerService = new CustomerService();

        $this->agents = $this->agentService->getAgent()->select([\DB::raw('lower(name) as name'), 'id'])->pluck('id', 'name')->toArray();
        $this->products = $this->productService->getProduct()
            ->select([\DB::raw('lower(name) as name'), 'id'])
            ->where('type', 'b')
            ->pluck('id', 'name')->toArray();
        $this->customers = $this->customerService->getCustomers()
            ->select([\DB::raw('lower(name) as name'), 'id'])
            ->pluck('id', 'name')
            ->toArray();
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
        $month = trim($row[4]);
        $year = trim($row[5]);
        $customerName = trim($row[6]);
        $productName = trim($row[7]);
        $value = trim($row[8]);

        $errors = "";
        try {
            //VALIDATION EMPTY
            if (empty($name)) $errors .= "\n\t-Kolom nama tidak boleh kosong";
            if (empty($code)) $errors .= "\n\t-Kolom kode tidak boleh kosong";
            if (empty($month)) $errors .= "\n\t-Kolom bulan tidak boleh kosong";
            if (empty($year)) $errors .= "\n\t-Kolom tahun tidak boleh kosong";
            if (empty($productName)) $errors .= "\n\t-Kolom produk tidak boleh kosong";
            if (empty($customerName)) $errors .= "\n\t-Kolom customer tidak boleh kosong";
            if (empty($value)) $errors .= "\n\t-Kolom nilai tidak boleh kosong";

            //VALIDATION MASTER
            if (!array_key_exists(trim(strtolower($name)), $this->agents)) $errors .= "\n\t-Kolom sales tidak terdaftar " . $name;
            if (!array_key_exists(trim(strtolower($productName)), $this->products)) $errors .= "\n\t-Kolom produk tidak terdaftar " . $productName;
            if (!array_key_exists(trim(strtolower($customerName)), $this->customers)) $errors .= "\n\t-Kolom customer tidak terdaftar " . $customerName;

            $now = now()->format("[Y-m-d H:i:s]");
            if (!empty($errors)) {
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : " . $errors);
            } else {

                $arrData = [
                    'agent_id' => $this->agents[trim(strtolower($name))],
                    'product_id' => $this->products[trim(strtolower($productName))],
                    'customer_id' => $this->customers[trim(strtolower($customerName))],
                    'month' => $month,
                    'year' => $year,
                    'value' => $value,
                ];

                $idExist = '';
                $checkExist = $this->agentBoosterService->getAgentBoosters()
                    ->select(['id', 'agent_id'])
                    ->where('agent_id', $this->agents[trim(strtolower($name))])
                    ->where('month', $month)
                    ->where('year', $year)
                    ->where('product_id', $arrData['product_id'])
                    ->where('customer_id', $arrData['customer_id'])
                    ->first();
                if ($checkExist) $idExist = $checkExist->id;

                $request = new AgentBoosterRequest();
                $request->merge($arrData);

                $this->agentBoosterService->save($request, $idExist);

                $now = now()->format("[Y-m-d H:i:s]");
                $storage->append($this->logname, "{$now} SUCESS {$no} {$name}");
            }
        } catch (\Throwable $th) {
            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "{$now} ERROR {$no} {$name} {$th->getMessage()}");
        }
    }
}
