<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\PSA\ActivityDetailService;
use App\Services\PSA\AgentService;
use App\Services\PSA\ProductService;
use App\Services\PSA\TargetAgentDetailService;
use App\Services\PSA\TargetUnitDetailService;
use App\Services\PSA\TypeService;
use App\Services\Setting\AppMasterDataService;
use Carbon\Carbon;

class LeaderController extends Controller
{
    private ProductService $productService;
    private AgentService $agentService;
    private TargetUnitDetailService $targetUnitDetailService;
    private TargetAgentDetailService $targetAgentDetailService;
    private AppMasterDataService $appMasterDataService;
    private ActivityDetailService $activityDetailService;
    private TypeService $typeService;

    public function __construct()
    {
        $this->productService = new ProductService();
        $this->agentService = new AgentService();
        $this->targetUnitDetailService = new TargetUnitDetailService();
        $this->appMasterDataService = new AppMasterDataService();
        $this->targetAgentDetailService = new TargetAgentDetailService();
        $this->activityDetailService = new ActivityDetailService();
        $this->typeService = new TypeService();
    }

    public function getProducts()
    {
        try {
            if(\Auth::check()){
                $products = $this->productService->getProduct()->get();
                return response()->json([
                    'message' => 'Success',
                    'data' => $products
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

    public function getAgents()
    {
        try {
            $getMaster = $this->appMasterDataService->getMasterByCode('PMP001');
            if(\Auth::check()){
                $agents = $this->agentService->getAgent()
                    ->where('position_id', $getMaster->id)
                    ->with(['type', 'position'])
                    ->get();

                foreach ($agents as $agent){
                    $agent->totalPending = $this->activityDetailService->getDetails()
                         ->whereHas('activity', function ($query) use ($agent) {
                             $query->where('agent_id', $agent->id);
                         })
                        ->whereNull('approved_status')
                        ->where('type', 2)
                        ->count();
                }
                return response()->json([
                    'message' => 'Success',
                    'data' => $agents
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

    public function getAgent(String $id)
    {
        try {
            if(\Auth::check()){
                $agent = $this->agentService->getAgentById($id);
                $agent->load(['type', 'position']);
                return response()->json([
                    'message' => 'Success',
                    'data' => $agent
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

    public function getTargets(string $productId, string $month, string $year)
    {
        try {
            if(\Auth::check()){
                // Fetch targets with the 'targetUnit' relationship loaded
                $targets = $this->targetUnitDetailService->getDetails()
                    ->with('targetUnit')  // Eager load the 'targetUnit' relationship
                    ->whereHas('targetUnit', function($query) use ($productId) {
                        $query->where('product_id', $productId);
                    })
//                    ->where('target_unit_id', '52956c2e-e0cd-4c8b-a7d9-86468f2c6d2c')
                    ->where('month', $month)
                    ->where('year', $year)
                    ->get();

                $sortedTargets = $targets->sortBy(function ($target) {
                    return $target->targetUnit->unit->order;  // Or any other attribute you are interested in
                });

                $startDate = $year.'-01-01';
                $carbonDate = Carbon::create($year, $month, 1);
                $endDate = $year.'-'.$month.'-'.$carbonDate->endOfMonth()->format('d');
                $realizations = $this->activityDetailService->getDetails()
                    ->whereHas('activity', function($query) use ($productId, $month, $year){
                        $query->where('product_id', $productId);
                    })
                    ->with(['activity', 'typeActivity'])
                    ->whereBetween('realization_date', [$startDate, $endDate]);

                foreach ($sortedTargets as $target) {
                    $target->unit_id = $target->targetUnit->unit_id;
                    $target->unit_name = $target->targetUnit->unit->name;
                    $target->type = $target->targetUnit->type;
                    $target->product = $target->targetUnit->product;
                    $realizationQuery = clone $realizations;
                    $realizationQuery->whereHas('activity.agent', function($query) use ($target){
                        $query->where('unit_id', $target->unit_id);
                    });
                    $target->mtd = (int)$realizationQuery->sum('closing_amount');

                    unset($target->targetUnit);
                }

                return response()->json([
                    'message' => 'Success',
                    'data' => $sortedTargets->values()->all()
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

    public function getTargetAgents(string $productId, string $month, string $year)
    {
        try {
            if(\Auth::check()){
                $targets = $this->targetAgentDetailService->getDetails()
                    ->whereHas('targetAgent', function($query) use ($productId){
                        $query->where('product_id', $productId);
                    })
                    ->where('month', $month)
                    ->where('year', $year)
                    ->get();
                $startDate = $year.'-01-01';
                $carbonDate = Carbon::create($year, $month, 1);
                $endDate = $year.'-'.$month.'-'.$carbonDate->endOfMonth()->format('d');
                $realizations = $this->activityDetailService->getDetails()
                    ->whereHas('activity', function($query) use ($productId, $month, $year){
                        $query->where('product_id', $productId);
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
                    $target->mtd = $realizations->where('activity.agent_id', $target->agent_id)->sum('closing_amount');
                    if($target->type == '1') {
                        $target->order = $target->targetAgent->product->order;
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
