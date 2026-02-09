<?php
namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\PlanRequest;
use App\Http\Requests\PSA\RealizationRequest;
use App\Models\Attendance\Attendance;
use App\Models\PSA\Plan;
use App\Repositories\PSA\RealizationRepository;
use App\Models\PSA\Realization;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RealizationService extends Controller
{
    private RealizationRepository $realizationRepository;
    private PlanService $planService;
    public function __construct()
    {
        $this->realizationRepository = new RealizationRepository(
            new Realization()
        );
        $this->planService = new PlanService();
    }

    public function getRealizations($columns = []): Builder
    {
        $query = $this->realizationRepository->query();

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getRealizationById(string $id): Realization
    {
        return $this->realizationRepository->getById($id);
    }

    public function getRealizationsForArray(): array
    {
        return $this->getRealizations(['id', 'name'])->pluck('name', 'id')->toArray();
    }

    public function save(RealizationRequest $request, string $id = ''): void
    {
        $plan = $this->planService->getPlanById($id);
        $fields = [
            'plan_id' => $plan->id,
            'agent_id' => $plan->agent_id,
            'title' => $plan->title,
            'customer_id' => $plan->customer_id,
            'date' => resetDate($request->date),
            'time' => $request->time,
            'product_id' => $request->product_id,
            'type_id' => $request->type_id,
            'note' => $request->note,
            'closing_amount' => resetCurrency($request->closing_amount),
            'account_number' => $request->account_number,
            'approved_by' => $request->approved_by,
            'approved_status' => $request->approved_status,
            'approved_date' => resetDate($request->approved_date),
            'approved_note' => $request->approved_note,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
        ];

        $realization = $this->realizationRepository->updateOrCreate($fields, [
            'plan_id' => $plan->id,
        ]);

        defaultUploadFile($realization, $request, Realization::FILE_PATH, 'file_' . Str::slug($realization->title) . '_' . time());
    }

    public function saveRealization(RealizationRequest $request, string $id = '')
    {
        $fields = [
            'plan_id' => $request->plan_id,
            'agent_id' => $request->agent_id,
            'title' => $request->title,
            'customer_id' => $request->customer_id,
            'date' => resetDate($request->date),
            'time' => $request->time,
            'product_id' => $request->product_id,
            'type_id' => $request->type_id,
            'note' => $request->note,
            'closing_amount' => resetCurrency($request->closing_amount),
            'account_number' => $request->account_number,
            'approved_by' => $request->approved_by,
            'approved_status' => $request->approved_status,
            'approved_date' => resetDate($request->approved_date),
            'approved_note' => $request->approved_note,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
        ];

        if ($id != '') {
            $realization = $this->realizationRepository->update($fields, $id);
        } else {
            $realization = $this->realizationRepository->create($fields);
        }

        return $realization;
    }

    public function delete(string $id): void
    {
        $this->realizationRepository->destroy($id);
    }
}
