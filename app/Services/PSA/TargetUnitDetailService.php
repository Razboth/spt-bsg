<?php
namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\TargetUnitRequest;
use App\Models\PSA\TargetUnit;
use App\Repositories\PSA\TargetUnitDetailRepository;
use App\Models\PSA\TargetUnitDetail;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class TargetUnitDetailService extends Controller
{
    private TargetUnitDetailRepository $targetUnitDetailRepository;
    public function __construct()
    {
        $this->targetUnitDetailRepository = new TargetUnitDetailRepository(
            new TargetUnitDetail()
        );
    }

    public function getDetails($columns = []): Builder
    {
        $query = $this->targetUnitDetailRepository->query();

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getDetailById(string $id): TargetUnitDetail
    {
        return $this->targetUnitDetailRepository->getById($id);
    }

    public function getDetailForArray(): array
    {
        return $this->getDetails(['id', 'name'])->pluck('name', 'id')->toArray();
    }

    public function save(array $arr): void
    {
        $this->targetUnitDetailRepository->updateOrCreate([
            'amount' => resetCurrency($arr['amount']),
            ],[
            'target_unit_id' => $arr['target_unit_id'],
            'month' => $arr['month'],
            'year' => $arr['year'],
        ]);
    }
}
