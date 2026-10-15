<?php
namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Services\PSA\ActivityDetailService;
use App\Services\PSA\ActivityService;
use App\Services\PSA\AgentService;
use App\Services\PSA\CustomerService;
use App\Services\PSA\ProductService;
use App\Services\PSA\SettingPositionProductService;
use App\Services\PSA\TargetAgentService;
use App\Services\PSA\TargetPeriodService;
use App\Services\PSA\TargetUnitService;
use App\Services\PSA\TypeService;
use App\Services\Setting\AppMasterDataService;
use App\Services\Setting\AppParameterService;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    private AppMasterDataService $appMasterDataService;
    private TargetPeriodService $targetPeriodService;
    private TypeService $typeService;
    private ProductService $productService;
    private ActivityService $activityService;
    private TargetUnitService $targetUnitService;
    private AgentService $agentService;
    private ActivityDetailService $activityDetailService;
    private CustomerService $customerService;
    private TargetAgentService $targetAgentService;
    private SettingPositionProductService $settingPositionProductService;
    private AppParameterService $appParameterService;

    public function __construct()
    {
        $this->appMasterDataService          = new AppMasterDataService();
        $this->targetPeriodService           = new TargetPeriodService();
        $this->typeService                   = new TypeService();
        $this->productService                = new ProductService();
        $this->activityService               = new ActivityService();
        $this->targetUnitService             = new TargetUnitService();
        $this->agentService                  = new AgentService();
        $this->activityDetailService         = new ActivityDetailService();
        $this->customerService               = new CustomerService();
        $this->targetAgentService            = new TargetAgentService();
        $this->settingPositionProductService = new SettingPositionProductService();
        $this->appParameterService           = new AppParameterService();
    }

    public function index(Request $request)
    {
        $units = $this->appMasterDataService->getMasters('PMUK')
            ->when(\Auth::user()->role->name == 'Manager', function ($query) {
                $query->where('id', \Auth::user()->employee()?->unit_id);
            })
            ->pluck('name', 'id')->toArray();
        $periods = $this->targetPeriodService->getTargetPeriods()->get();

        $arrPeriods = [];
        foreach ($periods as $period) {
            $arrPeriods[$period->id] = setDate($period->start_date, 'd F Y') . ' - ' . setDate($period->end_date, 'd F Y');
        }

        $filterYear   = $request->input('filterYear') ?? array_key_last($arrPeriods);
        $filterBranch = $request->input('filterBranch') ?? '';
        $filterAgent  = $request->input('filterAgent') ?? '';
        $period       = $this->targetPeriodService->getTargetPeriodById($filterYear);

        $arrMonth = [];
        //create carbon diff for each month in the period
        $startDate = \Carbon\Carbon::parse($period->start_date);
        $endDate   = \Carbon\Carbon::parse($period->end_date);

        list($year, $month, $date) = explode("-", $period->start_date);

        $diffInMonths = $startDate->diffInMonths($endDate);
        for ($i = 0; $i <= $diffInMonths; $i++) {
            $arrMonth[$startDate->copy()->addMonths($i)->format('m')] = $startDate->copy()->addMonths($i)->format('F Y');
        }

        $filterMonth    = $request->input('filterMonth') ?? date('m');
        $startMonthDate = $year . "-" . $filterMonth . "-01";
        //carbon get last day
        $endMonthDate = \Carbon\Carbon::parse($startMonthDate)->endOfMonth()->format('Y-m-d');

        if (\Auth::user()->role_id == 9) {
            $filterAgent = \Auth::user()->model_id;
        }

        if (\Auth::user()->role->name == 'Manager' || \Auth::user()->role->name == 'Pemimpin Cabang') {
            $filterBranch = \Auth::user()->employee()?->unit_id;
        }

        $arrPointTypes = $this->typeService->getTypes()->pluck('point', 'id')->toArray();

        $targetUnits = $this->targetUnitService->getTargetUnits()
            ->with('details')
            ->where('period_id', $filterYear)
            ->when($filterBranch, function ($query, $filterBranch) {
                return $query->where('unit_id', $filterBranch);
            })
            ->get();

        $targetUnitAccount = 0;
        $targetUnitPoint   = 0;
        foreach ($targetUnits as $target) {
            $detail = $target->details->where('month', $filterMonth)->first();
            if ($detail) {
                if ($target->type == 1) {
                    $targetUnitAccount += $detail->amount;
                } else {
                    $targetUnitPoint += $detail->amount;
                }
            }
        }

        $positionProducts = [];
        if ($filterAgent) {
            $agent               = $this->agentService->getAgentById($filterAgent);
            $positionProducts    = $this->settingPositionProductService->getProductsByPosition($agent?->position_id);
            $targetAgentAccounts = $this->targetAgentService->getTargetAgents()
                ->where('target_period_id', $filterYear)
                ->when($filterAgent, function ($query, $filterAgent) {
                    return $query->where('agent_id', $filterAgent);
                })
                ->where('type', 1)
                ->get();
            $targetAgentAccount = 0;
            foreach ($targetAgentAccounts as $targetAgent) {
                $detail = $targetAgent->details->where('month', $filterMonth)->first();
                if ($detail) {
                    $targetAgentAccount += $detail->amount;
                }
            }

            $targetAgentPoints = $this->targetAgentService->getTargetAgents()
                ->where('target_period_id', $filterYear)
                ->when($filterAgent, function ($query, $filterAgent) {
                    return $query->where('agent_id', $filterAgent);
                })
                ->where('type', 2)
                ->get();
            $targetAgentPoint = 0;
            foreach ($targetAgentPoints as $targetAgent) {
                $detail = $targetAgent->details->where('month', $filterMonth)->first();
                if ($detail) {
                    $targetAgentPoint += $detail->amount;
                }
            }
        }

        $products = $this->productService->getProduct()
            ->when($filterAgent, function ($query) use ($positionProducts) {
                return $query->whereIn('id', $positionProducts);
            })
            ->where('type', 'p')
            ->get();

        $activities = $this->activityService->getActivities()
            ->with('details')
            ->when($filterAgent, function ($query, $filterAgent) {
                return $query->where('agent_id', $filterAgent);
            })
            ->when(\Auth::user()->role->name == 'Manager', function ($query) {
                $query->whereHas('agent', function ($query) {
//                    $query->where('leader_id', \Auth::user()->model_id);
                                    $query->where('unit_id', \Auth::user()->employee()?->unit_id);

                });
            })
            ->when($filterBranch, function ($query, $filterBranch) {
                return $query->whereHas('agent', function ($query) use ($filterBranch) {
                    $query->where('unit_id', $filterBranch);
                });
            })
            ->whereHas('details', function ($query) use ($startMonthDate, $endMonthDate) {
                return $query->whereBetween('realization_date', [$startMonthDate, $endMonthDate]);
            })
            ->get();

        $arrActivityAgent        = [];
        $arrActivityAgentClosing = [];
        $arrActivityUnit         = [];
        $arrActivityUnitClosing  = [];
        $arrMonthlySales         = [];
        $arrMonthlyPoint         = [];

        $totalPoint         = 0;
        $totalClosingAmount = 0;
        foreach ($activities as $activity) {
            if (! isset($arrActivityAgent[$activity->agent_id])) {
                $arrActivityAgent[$activity->agent_id] = 0;
            }
            $arrActivityAgent[$activity->agent_id] += 1;

            $activityAgent = $this->agentService->getAgentById($activity->agent_id);
            if (! isset($arrActivityUnit[$activityAgent->unit_id])) {
                $arrActivityUnit[$activityAgent->unit_id] = 0;
            }
            $arrActivityUnit[$activityAgent->unit_id] += 1;

            $activity->details->each(function ($detail) use ($arrPointTypes, $activity, $activityAgent, &$totalClosingAmount, &$totalCustomer, &$totalPoint, &$arrProductPerformance, &$arrActivityAgentClosing, &$arrActivityUnitClosing, &$arrMonthlySales, &$arrMonthlyPoint) {
                $totalClosingAmount += $detail->closing_amount;

                list($year, $month, $date) = explode("-", $activity->created_at);
                $month                     = (int) $month;

                if ($detail->closing_amount > 0) {
                    //check only type sales
                    if ($activity->activity_type == 'd') {
                        if (! isset($arrActivityAgentClosing[$activity->agent_id])) {
                            $arrActivityAgentClosing[$activity->agent_id] = 0;
                        }
                        $arrActivityAgentClosing[$activity->agent_id] += 1;

                        if (! isset($arrActivityUnitClosing[$activityAgent->unit_id])) {
                            $arrActivityUnitClosing[$activityAgent->unit_id] = 0;
                        }
                        $arrActivityUnitClosing[$activityAgent->unit_id] += 1;
                    }

                    if (! isset($arrMonthlySales[$month])) {
                        $arrMonthlySales[$month] = 0;
                    }
                    $arrMonthlySales[$month] += $detail->closing_amount;
                }

                if (! isset($arrMonthlyPoint[$activity->agent_id])) {
                    $arrMonthlyPoint[$month] = 0;
                }
                $arrMonthlyPoint[$month] += $arrPointTypes[$detail->type_id];

                if (! isset($arrProductPerformance[$activity->product_id])) {
                    $arrProductPerformance[$activity->product_id] = 0;
                }
                $arrProductPerformance[$activity->product_id] += $detail->closing_amount;

                if (isset($arrPointTypes[$detail->type_id])) {
                    $totalPoint += $arrPointTypes[$detail->type_id];
                }

            });
        }

        $arrTotalTarget = [];
        $targets        = $this->targetUnitService->getTargetUnits()->get();
        foreach ($targets as $target) {
            if (! isset($arrTotalTarget[$target->product_id])) {
                $arrTotalTarget[$target->product_id] = 0;
            }

            $arrTotalTarget[$target->product_id] += $target->total;
        }

        foreach ($products as $product) {
            $product->target      = isset($arrTotalTarget[$product->id]) ? $arrTotalTarget[$product->id] : 0;
            $product->realization = isset($arrProductPerformance[$product->id]) ? $arrProductPerformance[$product->id] : 0;
            $product->percentage  = $product->target > 0 && $product->realization > 0 ? ($product->realization / $product->target) * 100 : 0;
        }

        $agents = $this->agentService->getAgent(isSalesOnly: true)
            ->when(\Auth::user()->role->name == 'Manager', function ($query) {
//                $query->where('leader_id', \Auth::user()->model_id);
                                $query->where('unit_id', \Auth::user()->employee()?->unit_id);

            })
            ->when($filterBranch, function ($query, $filterBranch) {
                return $query->where('unit_id', $filterBranch);
            })
            ->pluck('name', 'id')->toArray();

        $agentData = $this->agentService->getAgent()
            ->where('rank_id', $this->appParameterService->getParameterByCode('PS')->value)
            ->when(\Auth::user()->role->name == 'Manager', function ($query) {
//                $query->where('leader_id', \Auth::user()->model_id);
                                $query->where('unit_id', \Auth::user()->employee()?->unit_id);

            })
            ->get();

        foreach ($agentData as $data) {
            $data->success_rate = isset($arrActivityAgentClosing[$data->id]) && $arrActivityAgentClosing[$data->id] > 0 && $arrActivityAgent[$data->id] > 0 ? $arrActivityAgentClosing[$data->id] / $arrActivityAgent[$data->id] * 100 : 0;
        }

        $unitData = $this->appMasterDataService->getMasters('PMUK')->orderBy('code')->limit(10)->get();

        foreach ($unitData as $data) {
            $data->success_rate = isset($arrActivityUnitClosing[$data->id]) && $arrActivityUnitClosing[$data->id] > 0 && $arrActivityUnit[$data->id] > 0 ? $arrActivityUnitClosing[$data->id] / $arrActivityUnit[$data->id] * 100 : 0;
        }

        $deltaAmount = $targetUnitAccount - $totalClosingAmount;
        $deltaPoint  = $targetUnitPoint - $totalPoint;

        return view('psa.performance.index', [
            'products'           => $products,
            'units'              => $units,
            'arrPeriods'         => $arrPeriods,
            'agents'             => $agents,
            'agentData'          => $agentData,
            'unitData'           => $unitData,
            'filterYear'         => $filterYear,
            'filterBranch'       => $filterBranch,
            'filterAgent'        => $filterAgent,
            'arrMonthlySales'    => $arrMonthlySales,
            'arrMonthlyPoint'    => $arrMonthlyPoint,
            'targetUnitAccount'  => $targetUnitAccount,
            'targetUnitPoint'    => $targetUnitPoint,
            'targetAgentAccount' => $targetAgentAccount ?? 0,
            'targetAgentPoint'   => $targetAgentPoint ?? 0,
            'totalPoint'         => $totalPoint,
            'totalClosingAmount' => $totalClosingAmount,
            'deltaAmount'        => $deltaAmount,
            'deltaPoint'         => $deltaPoint,
        ]);
    }

    public function performanceTargetDetail($periodId, $productId)
    {
        $period     = $this->targetPeriodService->getTargetPeriodById($periodId);
        $activities = $this->activityDetailService->getDetails()
            ->whereHas('activity', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->where('closing_amount', '>', 0)
            ->whereBetween('realization_date', [$period->start_date, $period->end_date])
            ->get();
        return view('psa.performance.target-detail', [
            'activities' => $activities,
        ]);
    }

    public function performanceAgentDetail(Request $request, $periodId, $agentId)
    {
        $agent = $this->agentService->getAgentById($agentId);
        $types = $this->typeService->getTypes()->pluck('name', 'id')->toArray();

        $period = $this->targetPeriodService->getTargetPeriodById($periodId);

        $filterStartDate = $request->input('filterStartDate') ? resetDate($request->input('filterStartDate')) : $period->start_date;
        $filterEndDate   = $request->input('filterEndDate') ? resetDate($request->input('filterEndDate')) : $period->end_date;

        $activities = $this->activityService->getActivities()
            ->where('agent_id', $agentId)
            ->whereBetween('created_at', [$filterStartDate, $filterEndDate])
            ->get();

        $arrPointTypes = $this->typeService->getTypes()->pluck('point', 'id')->toArray();

        $totalClosing    = 0;
        $arrActivity     = [];
        $arrCustomerData = [];
        foreach ($activities as $activity) {
            if (! isset($arrActivityAgent[$activity->agent_id])) {
                $arrActivityAgent[$activity->agent_id] = 0;
            }
            $arrActivityAgent[$activity->agent_id] += 1;
            $activity->details->each(function ($detail) use ($activity, $arrPointTypes, &$arrActivity, &$totalClosing, &$arrCustomerData) {
                if ($detail->closing_amount > 0) {
                    $totalClosing++;
                }

                if (! isset($arrActivity[$activity->product_id])) {
                    $arrActivity[$detail->type_id] = 0;
                }

                $arrActivity[$detail->type_id] += 1;

                if (! isset($arrCustomerData[$activity->customer_id][$activity->product_id])) {
                    $arrCustomerData[$activity->customer_id][$activity->product_id] = 0;
                }

                $arrCustomerData[$activity->customer_id][$activity->product_id] += $arrPointTypes[$detail->type_id];
            });
        }

        $customers = $this->customerService->getCustomers()->get();
        $products  = $this->productService->getProduct()->get();

        $successRate = $totalClosing > 0 ? $totalClosing / $activities->count() * 100 : 0;

        return view('psa.performance.agent-detail', [
            'agent'           => $agent,
            'types'           => $types,
            'arrActivity'     => $arrActivity,
            'successRate'     => $successRate,
            'customers'       => $customers,
            'products'        => $products,
            'arrCustomerData' => $arrCustomerData,
            'filterStartDate' => $filterStartDate,
            'filterEndDate'   => $filterEndDate,
        ]);
    }

    public function performanceUnitDetail(Request $request, $periodId, $unitId)
    {
        $unit  = $this->appMasterDataService->getMasterById($unitId);
        $types = $this->typeService->getTypes()->pluck('name', 'id')->toArray();

        $period = $this->targetPeriodService->getTargetPeriodById($periodId);

        $filterStartDate = $request->input('filterStartDate') ? resetDate($request->input('filterStartDate')) : $period->start_date;
        $filterEndDate   = $request->input('filterEndDate') ? resetDate($request->input('filterEndDate')) : $period->end_date;

        $activities = $this->activityService->getActivities()
            ->whereHas('agent', function ($query) use ($unitId) {
                $query->where('unit_id', $unitId);
            })
            ->whereBetween('created_at', [$filterStartDate, $filterEndDate])
            ->get();

        $arrPointTypes = $this->typeService->getTypes()->pluck('point', 'id')->toArray();

        $totalClosing     = 0;
        $arrActivity      = [];
        $arrCustomerData  = [];
        $arrActivityAgent = [];
        foreach ($activities as $activity) {
            $activityAgent = $this->agentService->getAgentById($activity->agent_id);

            if (! isset($arrActivityAgent[$activityAgent->unit_id])) {
                $arrActivityAgent[$activityAgent->unit_id] = 0;
            }
            $arrActivityAgent[$activityAgent->unit_id] += 1;

            $activity->details->each(function ($detail) use ($activity, $arrPointTypes, &$arrActivity, &$totalClosing, &$arrCustomerData) {
                if ($detail->closing_amount > 0) {
                    $totalClosing++;
                }

                if (! isset($arrActivity[$activity->product_id])) {
                    $arrActivity[$detail->type_id] = 0;
                }

                $arrActivity[$detail->type_id] += 1;

                if (! isset($arrCustomerData[$activity->customer_id][$activity->product_id])) {
                    $arrCustomerData[$activity->customer_id][$activity->product_id] = 0;
                }

                $arrCustomerData[$activity->customer_id][$activity->product_id] += $arrPointTypes[$detail->type_id];
            });
        }

        $customers = $this->customerService->getCustomers()->get();
        $products  = $this->productService->getProduct()->get();

        $successRate = $totalClosing > 0 ? $totalClosing / $activities->count() * 100 : 0;

        return view('psa.performance.unit-detail', [
            'unit'            => $unit,
            'types'           => $types,
            'arrActivity'     => $arrActivity,
            'successRate'     => $successRate,
            'customers'       => $customers,
            'products'        => $products,
            'arrCustomerData' => $arrCustomerData,
            'filterStartDate' => $filterStartDate,
            'filterEndDate'   => $filterEndDate,
        ]);
    }
}
