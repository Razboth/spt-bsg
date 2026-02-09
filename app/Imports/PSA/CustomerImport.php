<?php

namespace App\Imports\PSA;

use App\Http\Requests\PSA\CustomerRequest;
use App\Services\PSA\AgentService;
use App\Services\PSA\CustomerService;
use App\Services\Setting\AppMasterDataService;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;

class CustomerImport implements ToModel, WithEvents
{
    public string $logname;
    private CustomerService $customerService;
    private AgentService $agentService;
    private AppMasterDataService $appMasterDataService;
    public array $customers;
    public array $agents;
    public array $masters;

    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "customer-import-$today.log";
        $this->customerService = new CustomerService();
        $this->agentService = new AgentService();
        $this->appMasterDataService = new AppMasterDataService();

        $this->masters = $this->appMasterDataService->getMasterForArray('PMKC', isForImport: true);
        $this->customers = $this->customerService->getCustomers()->select([\DB::raw('lower(name) as name'), 'id'])
            ->pluck('id', 'name')->toArray();
        $this->agents = $this->agentService->getAgent()->select(['employee_number', 'id'])
            ->pluck('id', 'employee_number')->toArray();
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
        $category = trim($row[1]);
        $name = trim($row[2]);
        $pic = trim($row[3]);
        $phoneNumber = trim($row[4]);
        $email = trim($row[5]);
        $address = trim($row[6]);
        $nipAgent = trim($row[7]);
        $nameAgent = trim($row[8]);

        $errors = "";
        try {
            //VALIDATION EMPTY
            if (empty($category)) $errors .= "\n\t-Kolom kategori tidak boleh kosong";
            if (empty($name)) $errors .= "\n\t-Kolom nama tidak boleh kosong";
            if (!empty($nipAgent) && !array_key_exists(trim($nipAgent), $this->agents)) $errors .= "\n\t-NIP petugas tidak terdaftar " . $nipAgent;

            //VALIDATION MASTER
            if (!array_key_exists(trim(strtolower($category)), $this->masters)) $errors .= "\n\t-Kolom kategori tidak terdaftar " . $category;

            $now = now()->format("[Y-m-d H:i:s]");
            if (!empty($errors)) {
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : " . $errors);
            } else {

                $arrData = [
                    'category_id' => $this->masters[trim(strtolower($category))],
                    'name' => $name,
                    'pic' => $pic,
                    'phone_number' => $phoneNumber,
                    'email' => $email,
                    'address' => $address,
                    'status' => 't',
                ];

                $idExist = '';
                $checkExist = $this->customerService->getCustomers()
                    ->select(['id', 'name'])
                    ->where('name', $name)
                    ->first();
                if ($checkExist) $idExist = $checkExist->id;

                $code = $checkExist?->code ?? $this->customerService->getLastNumber();

                $arrData['code'] = $code;

                $request = new CustomerRequest();
                $request->merge($arrData);

                $customer = $this->customerService->save($request, $idExist);

                if (!empty($nipAgent)) {
                    $agentId = $this->agents[trim($nipAgent)];
                    $customer->update([
                        'agent_id' => $agentId,
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
