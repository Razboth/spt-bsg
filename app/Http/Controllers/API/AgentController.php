<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PSA\Agent;
use App\Services\PSA\ActivityDetailService;
use App\Services\PSA\AgentService;
use App\Services\PSA\ProductService;
use App\Services\PSA\TargetAgentDetailService;
use App\Services\PSA\TargetAgentService;
use App\Services\PSA\TargetUnitService;
use App\Services\PSA\TypeService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    private TargetAgentDetailService $targetAgentDetailService;
    private ActivityDetailService $activityDetailService;
    private TypeService $typeService;

    public function __construct()
    {
        $this->targetAgentDetailService = new TargetAgentDetailService();
        $this->activityDetailService = new ActivityDetailService();
        $this->typeService = new TypeService();
    }

    public function getTarget(string $agentId, string $month, string $year)
    {
        try {
            if(\Auth::check()){
                $targets = $this->targetAgentDetailService->getDetails()
                    ->whereHas('targetAgent', function($query) use ($agentId){
                        $query->where('agent_id', $agentId);
                    })
                    ->where('month', $month)
                    ->where('year', $year)
                    ->get();
                $startDate = $year.'-01-01';
                $carbonDate = Carbon::create($year, $month, 1);
                $endDate = $year.'-'.$month.'-'.$carbonDate->endOfMonth()->format('d');
                $realizations = $this->activityDetailService->getDetails()
                    ->whereHas('activity', function($query) use ($agentId, $month, $year){
                        $query->where('agent_id', $agentId);
                    })
                    ->with(['activity', 'typeActivity'])
                    ->whereBetween('realization_date', [$startDate, $endDate])
                    ->get();
                foreach ($targets as $target){
                    $target->agent_id = $target->targetAgent->agent_id;
                    $target->agent_name = $target->targetAgent->agent->name;
                    $target->product_name = $target->targetAgent->type == '1' ? $target->targetAgent->product->name : $target->targetAgent->typeActivity->name;
                    $target->product_id = $target->targetAgent->product_id;
                    $target->type = $target->targetAgent->type;
                    $target->unit_id = $target->targetAgent->agent->unit_id;
                    if($target->type == '1') {
                        $target->mtd = $realizations->where('activity.product_id', $target->product_id)->sum('closing_amount');
                        $target->order = $target->targetAgent->product->order;
                    }else{
                        $target->mtd = $realizations->where('type_id', $target->product_id)->count() * $this->typeService->getTypeById($target->product_id)->point;
                        $target->order = $this->typeService->getTypeById($target->product_id)->order;
                    }

                    //unload targetAgent
                    unset($target->targetAgent);
                }
                return response()->json([
                    'message' => 'Success',
                    'data' => $targets
                ]);
            }else{
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed',
                'data' => $e->getMessage()
            ]);
        }
    }
}
