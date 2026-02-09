<?php

namespace App\Imports\PSA;

use App\Models\PSA\SalesRatio;
use App\Services\PSA\AgentService;
use App\Services\PSA\CustomerService;
use App\Services\PSA\ProductService;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Throwable;

class SalesRatioImport implements ToModel, WithEvents
{
    public string $logname;
    private CustomerService $customerService;
    private ProductService $productService;
    private AgentService $agentService;

    public array $customers;
    public array $products;
    public array $agents;

    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "customer-import-$today.log";
        $this->customerService = new CustomerService();
        $this->productService = new ProductService();
        $this->agentService = new AgentService();

        $this->products = $this->productService->getProduct()
            ->select([DB::raw('lower(name) as name'), 'id'])
            ->pluck('id', 'name')
            ->toArray();
        $this->customers = $this->customerService->getCustomers()
            ->select([DB::raw('lower(name) as name'), 'id'])
            ->pluck('id', 'name')->toArray();
        $this->agents = $this->agentService->getAgent()
            ->select([DB::raw('lower(name) as name'), 'id'])
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
        $date = trim($row[1]);
        $agent = trim($row[2]);
        $customer = trim($row[3]);
        $product = trim($row[4]);
        $nominal = trim($row[5]);

        $errors = "";
        try {
            //VALIDATION EMPTY
            if (empty($date)) $errors .= "\n\t-Kolom tanggal tidak boleh kosong";
            if (empty($agent)) $errors .= "\n\t-Kolom agent tidak boleh kosong";
            if (empty($customer)) $errors .= "\n\t-Kolom customer tidak boleh kosong";
            if (empty($product)) $errors .= "\n\t-Kolom produk tidak boleh kosong";
            if (empty($nominal)) $errors .= "\n\t-Kolom nominal tidak boleh kosong";

            $date = convertExcelDate($date);

            //VALIDATION MASTER
            if (!array_key_exists(trim(strtolower($agent)), $this->agents)) $errors .= "\n\t-Kolom sales tidak terdaftar " . $agent;
            if (!array_key_exists(trim(strtolower($customer)), $this->customers)) $errors .= "\n\t-Kolom customer tidak terdaftar " . $customer;
            if (!array_key_exists(trim(strtolower($product)), $this->products)) $errors .= "\n\t-Kolom produk tidak terdaftar " . $product;

            if(!empty($date) && !Carbon::createFromFormat('Y-m-d', $date)) {
                $errors .= "\n\t-Kolom tanggal mulai tidak valid " . $date;
            }

            $now = now()->format("[Y-m-d H:i:s]");
            if (!empty($errors)) {
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$agent} TERKENA VALIDASI : " . $errors);
            } else {

                $arrData = [
                    'date' => $date,
                    'agent_id' => $this->agents[trim(strtolower($agent))],
                    'customer_id' => $this->customers[trim(strtolower($customer))],
                    'product_id' => $this->products[trim(strtolower($product))],
                    'nominal' => $nominal,
                ];

                SalesRatio::updateOrCreate([
                    'date' => $arrData['date'],
                    'agent_id' => $arrData['agent_id'],
                    'customer_id' => $arrData['customer_id'],
                    'product_id' => $arrData['product_id'],
                ], [
                    'nominal' => $arrData['nominal'],
                ]);

                $now = now()->format("[Y-m-d H:i:s]");
                $storage->append($this->logname, "$now SUCESS {$no} {$agent}");
            }
        } catch (Throwable $th) {
            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "$now ERROR {$no} {$agent} {$th->getMessage()}");
        }
    }
}
