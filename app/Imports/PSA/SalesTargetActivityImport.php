<?php
namespace App\Imports\PSA;

use App\Models\PSA\Agent;
use App\Models\PSA\TargetAgent;
use App\Models\PSA\TargetAgentDetail;
use App\Models\PSA\TargetPeriod;
use App\Models\PSA\Type;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;

class SalesTargetActivityImport implements ToModel, WithEvents
{
    public string $logname;
    public array $agents;
    public array $types;

    public function __construct()
    {
        $today         = now()->format('Y-m-d');
        $this->logname = "sales-target-activity-import-$today.log";

        $this->types = Type::select([\DB::raw('lower(name) as name'), 'id'])
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
                $now     = now()->format("[Y-m-d H:i:s]");
                $storage->append($this->logname, "{$now} FINISH");
            },
        ];
    }

    /**
     * Columns: No | NIK | Nama | Jabatan | Aktifitas | Jumlah Bulan | Target Tahunan | Target Bulanan
     *           A     B     C       D          E             F               G                 H
     */
    public function model(array $row)
    {
        if (! is_numeric($row[0])) {
            return null;
        }

        $storage = Storage::disk("log");

        $no            = trim($row[0]);
        $code          = trim($row[1]);
        $name          = trim($row[2]);
        $actifitas     = trim($row[4]);
        $totalMonth    = trim($row[5]);
        $yearlyTarget  = trim($row[6]);
        $monthlyTarget = trim($row[7]);

        $errors = "";
        try {
            if (empty($code)) {
                $errors .= "\n\t-Kolom NIK tidak boleh kosong";
            }

            if (empty($name)) {
                $errors .= "\n\t-Kolom nama tidak boleh kosong";
            }

            if (empty($actifitas)) {
                $errors .= "\n\t-Kolom aktifitas tidak boleh kosong";
            }

            if (empty($totalMonth)) {
                $errors .= "\n\t-Kolom jumlah bulan tidak boleh kosong";
            }

            if (empty($yearlyTarget)) {
                $errors .= "\n\t-Kolom target tahunan tidak boleh kosong";
            }

            $normalizedCode = trim(str_replace('.', '', strtolower($code)));
            if (! array_key_exists($normalizedCode, $this->agents)) {
                $errors .= "\n\t-NIK tidak terdaftar: " . $code;
            }

            if (! array_key_exists(trim(strtolower($actifitas)), $this->types)) {
                $errors .= "\n\t-Aktifitas tidak terdaftar: " . $actifitas;
            }

            $now = now()->format("[Y-m-d H:i:s]");
            if (! empty($errors)) {
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : " . $errors);
            } else {
                $agentId = $this->agents[$normalizedCode];
                $typeId  = $this->types[trim(strtolower($actifitas))];
                $value   = ((float) $yearlyTarget / (int) $totalMonth);

                $getPeriod = TargetPeriod::orderBy('end_date', 'desc')->first();

                $target = TargetAgent::updateOrCreate([
                    'type'             => '2',
                    'target_period_id' => $getPeriod->id,
                    'product_id'       => $typeId,
                    'agent_id'         => $agentId,
                ], [
                    'total' => (float) $yearlyTarget,
                ]);

                for ($i = 1; $i <= (int) $totalMonth; $i++) {
                    TargetAgentDetail::updateOrCreate([
                        'target_agent_id' => $target->id,
                        'month'           => $i,
                        'year'            => explode('-', $getPeriod->end_date)[0],
                    ], [
                        'amount' => $value,
                    ]);
                }

                $storage->append($this->logname, "{$now} SUCCESS {$no} {$name}");
            }
        } catch (\Throwable $th) {
            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "{$now} ERROR {$no} {$name} {$th->getMessage()}");
        }
    }
}
