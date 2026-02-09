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
            ->where('target_period_id', $period->id)
            ->get();

        $activityAgentAll = $this->activityService->getActivities()
            ->with(['agent', 'product'])
            ->where('date', '>=', $period->start_date)
            ->where('date', '<=', $period->end_date)
            ->get();

        $targetAll = $this->targetUnitService->getTargetUnits()
            ->where('period_id', $period->id)
            ->get();

        $scores = SptScore::get();
        $agentBoosters = AgentBooster::get();
        $booms = SettingBoom::get();
        $products = Product::get();
        $settingProducts = SettingPositionProduct::get();

        $this->data['scores'] = $scores;
        $this->data['agentBoosters'] = $agentBoosters;
        $this->data['booms'] = $booms;
        $this->data['products'] = $products;
        $this->data['settingProducts'] = $settingProducts;

        foreach ($this->data['agents'] as $agent) {
            $agentId = $agent->id;
            if ($agent->rank_id != '7a0094e0-c97e-475f-a75e-5eedd4bb9cdd') {
                $targets = $targetAll->where('unit_id', $agent->unit_id);
                $activities = $activityAgentAll->filter(function ($activity) use ($agent) {
                    return $activity->agent && $activity->agent->unit_id == $agent->unit_id;
                });
            } else {
                $targets = $targetAgentAll->where('agent_id', $agentId);
                $activities = $activityAgentAll->where('agent_id', $agent->id);
            }

            $this->data['targets'] = $targets;
            $this->data['activities'] = $activities;
            $this->data['agent'] = $agent;

            $sheets[] = new ReportIndividualSPTSingleExport($this->data, $scores);
        }

        return $sheets;
    }
}
