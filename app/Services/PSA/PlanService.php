<?php
namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\AgentRequest;
use App\Http\Requests\PSA\PlanRequest;
use App\Models\PSA\Agent;
use App\Repositories\PSA\PlanRepository;
use App\Models\PSA\Plan;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanService extends Controller
{
    private PlanRepository $planRepository;
    public function __construct()
    {
        $this->planRepository = new PlanRepository(
            new Plan()
        );
    }

    public function getPlans($columns = []): Builder
    {
        $query = $this->planRepository->query();

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getPlanById(string $id): Plan
    {
        return $this->planRepository->getById($id);
    }

    public function getPlansForArray(): array
    {
        return $this->getPlans(['id', 'name'])->pluck('name', 'id')->toArray();
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getPlans();
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('title', 'like', '%' . $filter . '%')
                        ->orWhere('note', 'like', '%' . $filter . '%')
                        ->orWhere('number', 'like', '%' . $filter . '%');
                }
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'filename', 'type' => 'filename'],
                ['name' => 'product_id', 'type' => 'master_relationship', 'masters' => 'product'],
                ['name' => 'agent_id', 'type' => 'master_relationship', 'masters' => 'agent'],
                ['name' => 'customer_id', 'type' => 'master_relationship', 'masters' => 'customer'],
                ['name' => 'type_id', 'type' => 'master_relationship', 'masters' => 'type'],
                ['name' => 'date', 'type' => 'date'],
                ['name' => 'time', 'type' => 'custom', 'value' => function ($row) {
                    return setDateTime($row->date." ".$row->time);
                }],
            ]);
        }
    }

    /**
     * @throws Exception
     */
    public function dataRealizations(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getPlans()->with(['realization']);
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('title', 'like', '%' . $filter . '%')
                        ->orWhere('note', 'like', '%' . $filter . '%')
                        ->orWhere('number', 'like', '%' . $filter . '%');
                }
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'filename', 'type' => 'custom', 'value' => function ($row) {
                    return view('components.datatables.download', [
                        'url' => $row->realization?->filename,
                    ]);
                }],
                ['name' => 'title', 'type' => 'custom', 'value' => function ($row) {
                    return $row->realization?->title;
                }],
                ['name' => 'product_id', 'type' => 'custom', 'value' => function ($row) {
                    return $row->realization?->product->name;
                }],
                ['name' => 'agent_id', 'type' => 'custom', 'value' => function ($row) {
                    return $row->realization?->agent->name;
                }],
                ['name' => 'customer_id', 'type' => 'custom', 'value' => function ($row) {
                    return $row->realization?->customer->name;
                }],
                ['name' => 'type_id', 'type' => 'custom', 'value' => function ($row) {
                    return $row->realization?->type->name;
                }],
                ['name' => 'time', 'type' => 'custom', 'value' => function ($row) {
                    return $row->realization?->date ? setDateTime($row->realization->date." ".$row->realization->time) : '';
                }],
            ]);
        }
    }

    public function save(PlanRequest $request, string $id = ''): void
    {
        $fields = [
            'agent_id' => $request->agent_id,
            'customer_id' => $request->customer_id,
            'title' => $request->title,
            'number' => $request->number,
            'date' => resetDate($request->date),
            'time' => $request->time,
            'product_id' => $request->product_id,
            'type_id' => $request->type_id,
            'note' => $request->note,
        ];

        if ($id != '') {
            $plan = $this->planRepository->update($fields, $id);
        } else {
            $plan = $this->planRepository->create($fields);
        }

        defaultUploadFile($plan, $request, Plan::FILE_PATH, 'file_' . Str::slug($plan->title) . '_' . time());
    }

    public function delete(string $id): void
    {
        $this->planRepository->destroy($id);
    }

    public function getLastNumber(): string
    {
        return generateFormatNumber('FPR', 'plans');
    }
}
