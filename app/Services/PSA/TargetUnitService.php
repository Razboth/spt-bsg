<?php

namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\TargetAgentRequest;
use App\Http\Requests\PSA\TargetPeriodRequest;
use App\Http\Requests\PSA\TargetUnitRequest;
use App\Models\PSA\TargetAgent;
use App\Models\PSA\TargetAgentDetail;
use App\Models\PSA\TargetPeriod;
use App\Repositories\PSA\TargetUnitRepository;
use App\Models\PSA\TargetUnit;
use App\Services\Setting\AppParameterService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TargetUnitService extends Controller
{
    private TargetUnitRepository $targetUnitRepository;
    private TargetUnitDetailService $targetUnitDetailService;
    private TargetPeriodService $targetPeriodService;
    private TargetAgentService $targetAgentService;
    private AgentService $agentService;
    private AppParameterService $appParameterService;

    public function __construct()
    {
        $this->targetUnitRepository = new TargetUnitRepository(
            new TargetUnit()
        );
        $this->targetUnitDetailService = new TargetUnitDetailService();
        $this->targetPeriodService = new TargetPeriodService();
        $this->targetAgentService = new TargetAgentService();
        $this->agentService = new AgentService();
        $this->appParameterService = new AppParameterService();
    }

    public function getTargetUnits($columns = []): Builder
    {
        $query = $this->targetUnitRepository->query();

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getTargetUnitById(string $id): TargetUnit
    {
        return $this->targetUnitRepository->getById($id);
    }

    public function getTargetUnitForArray(): array
    {
        return $this->getTargetUnits(['id', 'name'])->pluck('name', 'id')->toArray();
    }

    public function save(TargetUnitRequest $request): void
    {
        $target = $this->targetUnitRepository->updateOrCreate([
            'total' => resetCurrency($request->total),
        ], [
            'period_id' => $request->period_id,
            'unit_id' => $request->unit_id,
            'product_id' => $request->product_id,
            'type' => $request->type,
        ]);

        $period = $this->targetPeriodService->getTargetPeriodById($request->period_id);
        $year = substr($period->start_date, 0, 4);

        foreach ($request->get('amount') as $key => $value) {
            $this->targetUnitDetailService->save([
                'target_unit_id' => $target->id,
                'month' => $key,
                'year' => $year,
                'amount' => resetCurrency($value)
            ]);
        }

        $agents = $this->agentService->getAgent()
            ->where('unit_id', $request->unit_id)
            ->where('rank_id', $this->appParameterService->getParameterByCode('PS')->value)
            ->get();

        $totalAgents = count($agents);

        foreach ($agents as $agent) {
            $target = TargetAgent::updateOrCreate([
                'target_period_id' => $request->period_id,
                'product_id' => $request->product_id,
                'type' => $request->type,
                'agent_id' => $agent->id,
            ], [
                'total' => resetCurrency($request->total) / $totalAgents,
            ]);

            foreach ($request->get('amount') as $key => $value) {
                TargetAgentDetail::updateOrCreate([
                    'target_agent_id' => $target->id,
                    'month' => $key,
                    'year' => $year,
                ], [
                    'amount' => resetCurrency($value) / $totalAgents,
                ]);
            }
        }
    }

    public function delete(string $id): void
    {
        $this->targetUnitRepository->destroy($id);
    }
}
