<?php
namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\TargetAgentRequest;
use App\Http\Requests\PSA\TargetUnitRequest;
use App\Models\PSA\TargetUnit;
use App\Repositories\PSA\TargetAgentRepository;
use App\Models\PSA\TargetAgent;
use Debugbar;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class TargetAgentService extends Controller
{
    private TargetAgentRepository $targetAgentRepository;
    private TargetPeriodService $targetPeriodService;
    private TargetAgentDetailService $targetAgentDetailService;
    public function __construct()
    {
        $this->targetAgentRepository = new TargetAgentRepository(
            new TargetAgent()
        );
        $this->targetPeriodService = new TargetPeriodService();
        $this->targetAgentDetailService = new TargetAgentDetailService();
    }

    public function getTargetAgents($columns = []): Builder
    {
        $query = $this->targetAgentRepository->query();

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getTargetAgentById(string $id): TargetAgent
    {
        return $this->targetAgentRepository->getById($id);
    }

    public function getTargetAgentForArray(): array
    {
        return $this->getTargetAgents(['id', 'name'])->pluck('name', 'id')->toArray();
    }

    public function save(TargetAgentRequest $request): void
    {
        $target = $this->targetAgentRepository->updateOrCreate([
            'total' => resetCurrency($request->total),
        ], [
            'target_period_id' => $request->target_period_id,
            'agent_id' => $request->agent_id,
            'product_id' => $request->product_id,
            'type' => $request->type,
        ]);

        $period = $this->targetPeriodService->getTargetPeriodById($request->target_period_id);
        $year = substr($period->start_date, 0, 4);

        foreach ($request->get('amount') as $key => $value){
            $this->targetAgentDetailService->save([
                'target_agent_id' => $target->id,
                'month' => $key,
                'year' => $year,
                'amount' => resetCurrency($value),
            ]);
        }
    }

    public function delete(string $id): void
    {
        $this->targetAgentRepository->destroy($id);
    }
}
