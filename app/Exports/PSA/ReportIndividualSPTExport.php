<?php

namespace App\Exports\PSA;

use App\Models\PSA\AgentBooster;
use App\Models\PSA\Product;
use App\Models\PSA\SettingBoom;
use App\Models\PSA\SettingPositionProduct;
use App\Models\PSA\SptScore;
use App\Services\PSA\ActivityService;
use App\Services\PSA\TargetAgentService;
use App\Services\PSA\TargetUnitService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

ini_set('memory_limit', '4000M');

class ReportIndividualSPTExport implements WithMultipleSheets
{
    private TargetAgentService $targetAgentService;
    private TargetUnitService $targetUnitService;
    private ActivityService $activityService;

    protected array $data;

    function __construct($data)
    {
        $this->data = $data;
        $this->targetAgentService = app(TargetAgentService::class);
        $this->targetUnitService = app(TargetUnitService::class);
        $this->activityService = app(ActivityService::class);
    }

    public function sheets(): array
    {
        $sheets = [];

        $period = $this->data['period'];

        $targetAgentAll = $this->targetAgentService->getTargetAgents()
            ->with('details')
            ->where('target_period_id', $period->id)
            ->get()
            ->groupBy('agent_id');

        $activityAgentAll = $this->activityService->getActivities()
            ->with(['agent', 'product', 'details'])
            ->where('date', '>=', $period->start_date)
            ->where('date', '<=', $period->end_date)
            ->get();

        $activitiesByAgent = $activityAgentAll->groupBy('agent_id');
        $activitiesByUnit = $activityAgentAll->groupBy(function ($activity) {
            return $activity->agent->unit_id ?? 'unknown';
        });

        $targetAll = $this->targetUnitService->getTargetUnits()
            ->with('details')
            ->where('period_id', $period->id)
            ->get()
            ->groupBy('unit_id');

        $scores = SptScore::get();
        $agentBoosters = AgentBooster::get()->groupBy('agent_id');
        $booms = SettingBoom::get()->groupBy('agent_id');
        $products = Product::get();
        $settingProducts = SettingPositionProduct::with('details')->get();

        // Data global yang tidak berubah per agent
        $globalData = [
            'period' => $period,
            'scores' => $scores,
            'products' => $products,
            'settingProducts' => $settingProducts,
        ];

        foreach ($this->data['agents'] as $agent) {
            $agentId = $agent->id;
            
            if ($agent->rank_id != '7a0094e0-c97e-475f-a75e-5eedd4bb9cdd') {
                $targets = $targetAll->get($agent->unit_id) ?? collect();
                $activities = $activitiesByUnit->get($agent->unit_id) ?? collect();
            } else {
                $targets = $targetAgentAll->get($agentId) ?? collect();
                $activities = $activitiesByAgent->get($agentId) ?? collect();
            }

            $agentData = array_merge($globalData, [
                'targets' => $targets,
                'activities' => $activities,
                'agent' => $agent,
                'agentBoosters' => $agentBoosters->get($agentId) ?? collect(),
                'booms' => $booms->get($agentId) ?? collect(),
            ]);

            $sheets[] = new ReportIndividualSPTSingleExport($agentData);
        }

        return $sheets;
    }
}
