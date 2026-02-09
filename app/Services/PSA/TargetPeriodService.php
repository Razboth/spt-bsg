<?php
namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\TargetPeriodRequest;
use App\Http\Requests\PSA\TypeRequest;
use App\Models\PSA\Product;
use App\Models\PSA\Type;
use App\Repositories\PSA\TargetPeriodRepository;
use App\Models\PSA\TargetPeriod;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TargetPeriodService extends Controller
{
    private TargetPeriodRepository $targetPeriodRepository;
    public function __construct()
    {
        $this->targetPeriodRepository = new TargetPeriodRepository(
            new TargetPeriod()
        );
    }

    public function getTargetPeriods($columns = []): Builder
    {
        $query = $this->targetPeriodRepository->query();

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getTargetPeriodById(string $id): TargetPeriod
    {
        return $this->targetPeriodRepository->getById($id);
    }

    public function getTargetPeriodForArray(): array
    {
        return $this->getTargetPeriods(['id', 'name'])
            ->orderBy('start_date', 'desc')
            ->pluck('name', 'id')->toArray();
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getTargetPeriods();
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('name', 'like', '%' . $filter . '%');
                }
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'filename', 'type' => 'filename'],
                ['name' => 'start_date', 'type' => 'custom', 'value' => function ($row) {
                    return setDate($row->start_date, 'd F Y') . ' - ' . setDate($row->end_date, 'd F Y');
                }],
                ['name' => 'approved_status', 'type' => 'approved_status', 'isCanApprove' => true],
                ['name' => 'approved_2_status', 'type' => 'approved_status', 'isCanApprove' => true, 'approved_type' => 2],
            ], true);
        }
    }

    public function save(TargetPeriodRequest $request, string $id = ''): void
    {
        $fields = [
            'name' => $request->name,
            'start_date' => resetDate($request->start_date),
            'end_date' => resetDate($request->end_date),
            'note' => $request->note,
        ];

        if ($id != '') {
            $period = $this->targetPeriodRepository->update($fields, $id);
        } else {
            $period = $this->targetPeriodRepository->create($fields);
        }

        defaultUploadFile($period, $request, TargetPeriod::FILE_PATH, 'file_' . Str::slug($period->name) . '_' . time());

    }

    public function delete(string $id): void
    {
        $this->targetPeriodRepository->destroy($id);
    }

    public function updateApproval(Request $request, string $id): void
    {
        $fields = [
            'approved_by' => $request->get('approved_by'),
            'approved_status' => $request->get('approved_status'),
            'approved_date' => resetDate($request->get('approved_date')),
            'approved_note' => $request->get('approved_note'),
        ];

        $this->targetPeriodRepository->update($fields, $id);
    }

    public function updateApproval2(Request $request, string $id): void
    {
        $fields = [
            'approved_2_by' => $request->get('approved_by'),
            'approved_2_status' => $request->get('approved_status'),
            'approved_2_date' => resetDate($request->get('approved_date')),
            'approved_2_note' => $request->get('approved_note'),
        ];

        $this->targetPeriodRepository->update($fields, $id);
    }
}
