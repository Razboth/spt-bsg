<?php

namespace App\Imports\PSA;

use App\Http\Requests\PSA\AgentRequest;
use App\Services\PSA\AgentService;
use App\Services\Setting\AppMasterDataService;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;

class AgentImport implements ToModel, WithEvents
{
    public string $logname;
    private AgentService $agentService;
    private AppMasterDataService $appMasterDataService;
    private array $positions;
    private array $employeeTypes;
    private array $leaders;
    private array $units;
    private array $statuses;

    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "customer-import-$today.log";
        $this->agentService = new AgentService();
        $this->appMasterDataService = new AppMasterDataService();

        $this->positions = $this->appMasterDataService->getMasterForArray('PMP', isForImport: true);
        $this->employeeTypes = $this->appMasterDataService->getMasterForArray('PMTP', isForImport: true);
        $this->leaders = $this->agentService->getAgent()->pluck('id', DB::raw('lower(name)'))->toArray();
        $this->units = $this->appMasterDataService->getMasterForArray('PMUK', isForImport: true);
        $this->statuses = $this->appMasterDataService->getMasterForArray('PMSP', isForImport: true);
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
        $employeeNumber = trim($row[1]);
        $name = trim($row[2]);
        $birthDate = trim($row[3]);
        $birthPlace = trim($row[4]);
        $phoneNumber = trim($row[5]);
        $email = trim($row[6]);
        $gender = trim($row[7]);
        $identityNumber = trim($row[8]);
        $address = trim($row[9]);
        $position = trim($row[10]);
        $employeeType = trim($row[11]);
        $leader = trim($row[12]);
        $unit = trim($row[13]);
        $startDate = trim($row[14]);
        $endDate = trim($row[15]);
        $status = trim($row[16]);

        $errors = "";
        try {
            //VALIDATION EMPTY
            if (empty($employeeNumber)) $errors .= "\n\t-Kolom nomor pegawai tidak boleh kosong";
            if (empty($name)) $errors .= "\n\t-Kolom nama tidak boleh kosong";
            if (empty($position)) $errors .= "\n\t-Kolom posisi tidak boleh kosong";
            if (empty($employeeType)) $errors .= "\n\t-Kolom tipe pegawai tidak boleh kosong";
            if (empty($startDate)) $errors .= "\n\t-Kolom tanggal mulai tidak boleh kosong";
            if (empty($status)) $errors .= "\n\t-Kolom status tidak boleh kosong";

            $startDate = convertExcelDate($startDate);
            $endDate = convertExcelDate($endDate);
            $birthDate = convertExcelDate($birthDate);

            //VALIDATION MASTER
            if (!array_key_exists(trim(strtolower($position)), $this->positions)) $errors .= "\n\t-Kolom posisi tidak terdaftar " . $position;
            if (!array_key_exists(trim(strtolower($employeeType)), $this->employeeTypes)) $errors .= "\n\t-Kolom tipe pegawai tidak terdaftar " . $employeeType;
            if (!array_key_exists(trim(strtolower($status)), $this->statuses)) $errors .= "\n\t-Kolom status tidak terdaftar " . $status;

            if (!empty($unit) && !array_key_exists(trim(strtolower($unit)), $this->units)) $errors .= "\n\t-Kolom unit tidak terdaftar " . $unit;
            if (!empty($leader) && !array_key_exists(trim(strtolower($leader)), $this->leaders)) $errors .= "\n\t-Kolom leader tidak terdaftar " . $leader;

            if(!empty($startDate) && !Carbon::createFromFormat('Y-m-d', $startDate)) {
                $errors .= "\n\t-Kolom tanggal mulai tidak valid " . $startDate;
            }

            if(!empty($endDate) && !Carbon::createFromFormat('Y-m-d', $endDate)) {
                $errors .= "\n\t-Kolom tanggal akhir tidak valid " . $endDate;
            }

            if (!empty($birthDate) && !Carbon::createFromFormat('Y-m-d', $birthDate)) {
                $errors .= "\n\t-Kolom tanggal lahir tidak valid " . $birthDate;
            }

            $now = now()->format("[Y-m-d H:i:s]");
            if (!empty($errors)) {
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : " . $errors);
            } else {

                $arrData = [
                    'employee_number' => $employeeNumber,
                    'name' => $name,
                    'birth_date' => setDate($birthDate),
                    'birth_place' => $birthPlace,
                    'phone_number' => $phoneNumber,
                    'email' => $email,
                    'gender' => $gender == 'Perempuan' ? 'F' : 'M',
                    'identity_number' => $identityNumber,
                    'address' => $address,
                    'position_id' => $this->positions[trim(strtolower($position))],
                    'type_id' => $this->employeeTypes[trim(strtolower($employeeType))],
                    'unit_id' => $this->units[trim(strtolower($unit))],
                    'leader_id' => $this->leaders[trim(strtolower($leader))],
                    'join_date' => setDate($startDate),
                    'leave_date' => setDate($endDate),
                    'status_id' => $this->statuses[trim(strtolower($status))],
                    'activity_type' => 'a',
                ];

                $idExist = '';
                $checkExist = $this->agentService->getAgent()
                    ->select(['id', 'name'])
                    ->where('name', $name)
                    ->where('employee_number', $employeeNumber)
                    ->first();
                if ($checkExist) $idExist = $checkExist->id;

                $request = new AgentRequest();
                $request->merge($arrData);

                $this->agentService->save($request, $idExist);

                $now = now()->format("[Y-m-d H:i:s]");
                $storage->append($this->logname, "{$now} SUCESS {$no} {$name}");
            }
        } catch (\Throwable $th) {
            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "{$now} ERROR {$no} {$name} {$th->getMessage()}");
        }
    }
}
