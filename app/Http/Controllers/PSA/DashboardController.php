<?php
namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Models\PSA\SptScore;
use App\Services\PSA\ActivityDetailService;
use App\Services\PSA\ActivityService;
use App\Services\PSA\AgentBoosterService;
use App\Services\PSA\AgentService;
use App\Services\PSA\ProductService;
use App\Services\PSA\SettingPositionProductService;
use App\Services\PSA\TargetAgentService;
use App\Services\PSA\TargetPeriodService;
use App\Services\PSA\TargetUnitService;
use App\Services\PSA\TypeService;
use App\Services\Setting\AppMasterDataService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private AppMasterDataService $appMasterDataService;
    private AgentService $agentService;
    private ActivityService $activityService;
    private TypeService $typeService;
    private TargetUnitService $targetUnitService;
    private TargetAgentService $targetAgentService;
    private ProductService $productService;
    private TargetPeriodService $targetPeriodService;
    private ActivityDetailService $activityDetailService;
    private SettingPositionProductService $settingPositionProductService;
    private AgentBoosterService $agentBoosterService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->appMasterDataService          = new AppMasterDataService();
        $this->agentService                  = new AgentService();
        $this->activityService               = new ActivityService();
        $this->typeService                   = new TypeService();
        $this->targetUnitService             = new TargetUnitService();
        $this->targetAgentService            = new TargetAgentService();
        $this->productService                = new ProductService();
        $this->targetPeriodService           = new TargetPeriodService();
        $this->activityDetailService         = new ActivityDetailService();
        $this->settingPositionProductService = new SettingPositionProductService();
        $this->agentBoosterService           = new AgentBoosterService();
    }

    public function index(Request $request)
    {
        $periods    = $this->targetPeriodService->getTargetPeriods()->get();
        $arrPeriods = [];
        foreach ($periods as $period) {
            $arrPeriods[$period->id] = setDate($period->start_date, 'd F Y') . ' - ' . setDate($period->end_date, 'd F Y');
        }

        $filterYear   = $request->input('filterYear') ?? array_key_last($arrPeriods);
        $filterBranch = $request->input('filterBranch') ?? '';
        $filterAgent  = $request->input('filterAgent') ?? '';

        if (\Auth::user()->role_id == 9) {
            $filterAgent = \Auth::user()->model_id;
        }

        if (\Auth::user()->role->name == 'Manager' || \Auth::user()->role->name == 'Pemimpin Cabang') {
            $filterBranch = \Auth::user()->employee()?->unit_id;
        }

        $agents = $this->agentService->getAgent(isSalesOnly: true)
            ->when($filterBranch, function ($query, $filterBranch) {
                return $query->where('unit_id', $filterBranch);
            })
            ->when(\Auth::user()->role->name == 'Manager', function ($query) {
//                $query->where('leader_id', \Auth::user()->model_id);
                                $query->where('unit_id', \Auth::user()->employee()?->unit_id);

            })
            ->when(\Auth::user()->role->name == 'Pemimpin Cabang', function ($query) {
                $query->where('unit_id', \Auth::user()->employee()?->unit_id);
            })
            ->pluck('name', 'id')
            ->toArray();

        $period = $this->targetPeriodService->getTargetPeriodById($filterYear);

        $arrMonth = [];
        //create carbon diff for each month in the period
        $startDate = \Carbon\Carbon::parse($period->start_date);
        $endDate   = \Carbon\Carbon::parse($period->end_date);

        list($year, $month, $date) = explode("-", $period->start_date);

        $diffInMonths = $startDate->diffInMonths($endDate);
        for ($i = 0; $i <= $diffInMonths; $i++) {
            $arrMonth[$startDate->copy()->addMonths($i)->format('m')] = $startDate->copy()->addMonths($i)->format('F Y');
        }

        $positionProducts = [];
        if ($filterAgent) {
            $agent            = $this->agentService->getAgentById($filterAgent);
            $positionProducts = $this->settingPositionProductService->getProductsByPosition($agent?->position_id);
        }

        if (\Auth::user()->role->name == 'Manager') {
            $agent            = $this->agentService->getAgentById(\Auth::user()->model_id);
            $positionProducts = $this->settingPositionProductService->getProductsByPosition($agent?->position_id);
        }

        $filterMonth = $request->input('filterMonth') ?? date('m');

        $targetUnitAccount = $this->targetUnitService->getTargetUnits()
            ->where('period_id', $filterYear)
            ->when($filterBranch, function ($query, $filterBranch) {
                return $query->where('unit_id', $filterBranch);
            })
            ->where('type', 1)
            ->sum('total');

        $totalClosingAmount = 0;

        $products = $this->productService->getProduct()
            ->when($filterAgent || \Auth::user()->role->name == 'Manager', function ($query) use ($positionProducts) {
                return $query->whereIn('id', $positionProducts);
            })
            ->where('type', 'p')
            ->pluck('name', 'id')
            ->toArray();

        $arrProductPerformance = [];
        $arrMonthlySales       = [];

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
            ->when(\Auth::user()->role->name == 'Pemimpin Cabang', function ($query) {
                $query->whereHas('agent', function ($query) {
                    $query->where('unit_id', \Auth::user()->employee()?->unit_id);
                });
            })
            ->when($filterBranch, function ($query, $filterBranch) {
                return $query->whereHas('agent', function ($query) use ($filterBranch) {
                    $query->where('unit_id', $filterBranch);
                });
            })
            ->whereHas('details', function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('realization_date', [$startDate, $endDate]);
            })
            ->get();
        foreach ($activities as $activity) {
            $activity->details->each(function ($detail) use ($activity, &$totalClosingAmount, &$arrProductPerformance, &$arrMonthlySales) {
                $totalClosingAmount += $detail->closing_amount;

                if (! isset($arrProductPerformance[$activity->product_id])) {
                    $arrProductPerformance[$activity->product_id] = 0;
                }
                $arrProductPerformance[$activity->product_id] += $detail->closing_amount;

                list($year, $month, $date) = explode("-", $activity->created_at);
                $month                     = (int) $month;
                if (! isset($arrMonthlySales[$month])) {
                    $arrMonthlySales[$month] = 0;
                }
                $arrMonthlySales[$month] += $detail->closing_amount;
            });
        }

        $detailActivities = $this->activityDetailService->getDetails()
            ->whereBetween('realization_date', [$startDate, $endDate])
            ->when($filterAgent, function ($query, $filterAgent) {
                return $query->whereHas('activity', function ($query) use ($filterAgent) {
                    $query->where('agent_id', $filterAgent);
                });
            })
            ->when(\Auth::user()->role->name == 'Manager', function ($query) {
                $query->whereHas('activity', function ($query) {
                    $query->whereHas('agent', function ($query) {
//                        $query->where('leader_id', \Auth::user()->model_id);
                                        $query->where('unit_id', \Auth::user()->employee()?->unit_id);

                    });
                });
            })
            ->when($filterBranch, function ($query, $filterBranch) {
                return $query->whereHas('activity', function ($query) use ($filterBranch) {
                    $query->whereHas('agent', function ($query) use ($filterBranch) {
                        $query->where('unit_id', $filterBranch);
                    });
                });
            })
            ->orderBy('realization_date', 'desc')
            ->get();

        $latestActivities  = $detailActivities->take(5);
        $closingActivities = $detailActivities->where('closing_amount', '>', 0)->take(5);

        $percentageAccount = $targetUnitAccount > 0 ? ($totalClosingAmount / $targetUnitAccount) * 100 : 0;

        $positionBoosters = [];
        /** BOOSTERS */
        if ($filterAgent) {
            $positionBoosters = $this->settingPositionProductService->getBoostersByPosition($agent?->position_id);
        }

        if (\Auth::user()->role->name == 'Manager') {
            $agent            = $this->agentService->getAgentById(\Auth::user()->model_id);
            $positionBoosters = $this->settingPositionProductService->getBoostersByPosition($agent?->position_id);
        }

        $boosters = $this->productService->getProduct()
            ->when($filterAgent, function ($query) use ($positionBoosters) {
                return $query->whereIn('id', $positionBoosters);
            })
            ->when(\Auth::user()->role->name == 'Manager', function ($query) use ($positionBoosters) {
                return $query->whereIn('id', $positionBoosters);
            })
            ->where('type', 'b')
            ->get();

        $arrBoosterPerformance = [];
        $agentBoosters         = $this->agentBoosterService->getAgentBoosters()->get();
        foreach ($agentBoosters as $booster) {
            if (! isset($arrBoosterPerformance[$booster->product_id])) {
                $arrBoosterPerformance[$booster->product_id] = 0;
            }
            $arrBoosterPerformance[$booster->product_id] += $booster->value;
        }

        $data = [
            'totalClosingAmount'    => $totalClosingAmount,
            'percentageAccount'     => round($percentageAccount),
            'arrProductPerformance' => $arrProductPerformance,
            'arrBoosterPerformance' => $arrBoosterPerformance,
            'arrMonthlySales'       => $arrMonthlySales,
            'products'              => $products,
            'boosters'              => $boosters ?? [],
        ];

        $units = $this->appMasterDataService->getMasters('PMUK')
            ->pluck('name', 'id')->toArray();

        return view('psa.dashboard.index', [
            'units'             => $units,
            'agents'            => $agents,
            'arrPeriods'        => $arrPeriods,
            'filterYear'        => $filterYear,
            'filterBranch'      => $filterBranch,
            'filterAgent'       => $filterAgent,
            'products'          => $products,
            'boosters'          => $boosters ?? [],
            'data'              => $data,
            'latestActivities'  => $latestActivities,
            'closingActivities' => $closingActivities,
            'arrMonth'          => $arrMonth,
            'filterMonth'       => $filterMonth,
        ]);
    }

    public function home(Request $request)
    {
        $arrPeriods = $this->targetPeriodService->getTargetPeriods()->pluck('name', 'id')->toArray();

        $filterYear = $request->input('filterYear') ?? array_key_last($arrPeriods);
        $period     = $this->targetPeriodService->getTargetPeriodById($filterYear);
        $arrMonth   = [];
        //create carbon diff for each month in the period
        $startDate = \Carbon\Carbon::parse($period->start_date);
        $endDate   = \Carbon\Carbon::parse($period->end_date);

        $settingPositions = $this->settingPositionProductService->getSettingPositionProducts()
            ->where('type', 'p')
            ->pluck('name', 'id')->toArray();

        list($year, $month, $date) = explode("-", $period->start_date);

        $diffInMonths = $startDate->diffInMonths($endDate);
        for ($i = 0; $i <= $diffInMonths; $i++) {
            $arrMonth[$startDate->copy()->addMonths($i)->format('m')] = $startDate->copy()->addMonths($i)->format('F Y');
        }

        $filterMonth    = $request->input('filterMonth') ?? date('m');
        $startMonthDate = $year . "-" . $filterMonth . "-01";
        //carbon get last day
        $endMonthDate = \Carbon\Carbon::parse($startMonthDate)->endOfMonth()->format('Y-m-d');

        $filterBranch  = $request->input('filterBranch') ?? '';
        $filterAgent   = $request->input('filterAgent') ?? '';
        $filterSetting = $request->input('filterSetting') ?? '';
        if (\Auth::user()->role_id == 9) {
            $filterAgent = \Auth::user()->model_id;
        }

        if (\Auth::user()->role->name == 'Manager' || \Auth::user()->role->name == 'Pemimpin Cabang') {
            $filterBranch = \Auth::user()->employee()?->unit_id;
        }

        $agents = $this->agentService->getAgent(isSalesOnly: true)
            ->when(\Auth::user()->role->name == 'Manager', function ($query) {
//                $query->where('leader_id', \Auth::user()->model_id);
                                $query->where('unit_id', \Auth::user()->employee()?->unit_id);

            })
            ->when(\Auth::user()->role->name == 'Pemimpin Cabang', function ($query) {
                $query->where('unit_id', \Auth::user()->employee()?->unit_id);
            })
            ->when($filterSetting, function ($query, $filterSetting) {
                $setting = $this->settingPositionProductService->getSettingPositionProductById($filterSetting);
                return $query->whereIn('position_id', explode(',', $setting->positions));
            })
            ->when($filterBranch, function ($query, $filterBranch) {
                return $query->where('unit_id', $filterBranch);
            })
            ->pluck('name', 'id')
            ->toArray();

        $arrPointTypes = $this->typeService->getTypes()->pluck('point', 'id')->toArray();

        $totalClosingAmount = 0;
        $totalCustomer      = 0;
        $totalPoint         = 0;

        $targetUnitAccounts = $this->targetUnitService->getTargetUnits()
            ->with('details')
            ->where('period_id', $filterYear)
            ->when($filterBranch, function ($query, $filterBranch) {
                return $query->where('unit_id', $filterBranch);
            })
            ->when($filterSetting, function ($query, $filterSetting) {
                $setting  = $this->settingPositionProductService->getSettingPositionProductById($filterSetting);
                $products = $setting->details->pluck('product_id');
                return $query->whereIn('product_id', $products);
            })
            ->where('type', 1)
            ->get();

        $targetUnitPoints = $this->targetUnitService->getTargetUnits()
            ->with('details')
            ->where('period_id', $filterYear)
            ->when($filterBranch, function ($query, $filterBranch) {
                return $query->where('unit_id', $filterBranch);
            })
            ->where('type', 2)
            ->get();

        $targetUnitAccount = 0;
        foreach ($targetUnitAccounts as $target) {
            $detail = $target->details->where('month', $filterMonth)->first();
            if ($detail) {
                $targetUnitAccount += $detail->amount;
            }
        }

        $targetUnitPoint = 0;
        foreach ($targetUnitPoints as $target) {
            $detail = $target->details->where('month', $filterMonth)->first();
            if ($detail) {
                $targetUnitPoint += $detail->amount;
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

        if (\Auth::user()->role->name == 'Manager') {
            $agent            = $this->agentService->getAgentById(\Auth::user()->model_id);
            $positionProducts = $this->settingPositionProductService->getProductsByPosition($agent?->position_id);
        }

        $products = $this->productService->getProduct()
            ->when($filterAgent, function ($query, $filterAgent) use ($positionProducts) {
                return $query->whereIn('id', $positionProducts);
            })
            ->when(\Auth::user()->role->name == 'Manager', function ($query) use ($positionProducts) {
                return $query->whereIn('id', $positionProducts);
            })
            ->when($filterSetting, function ($query, $filterSetting) {
                $setting  = $this->settingPositionProductService->getSettingPositionProductById($filterSetting);
                $products = $setting->details->pluck('product_id');
                return $query->whereIn('id', $products);
            })
            ->where('type', 'p')
            ->pluck('name', 'id')
            ->toArray();

        $arrProductPerformance = [];

        $activities = $this->activityService->getActivities()
            ->with('details')
            ->when($filterAgent, function ($query, $filterAgent) {
                return $query->where('agent_id', $filterAgent);
            })
            ->when($filterBranch, function ($query, $filterBranch) {
                return $query->whereHas('agent', function ($query) use ($filterBranch) {
                    $query->where('unit_id', $filterBranch);
                });
            })
            ->whereHas('details', function ($query) use ($startMonthDate, $endMonthDate) {
                return $query->whereBetween('realization_date', [$startMonthDate, $endMonthDate]);
            })
            ->when($filterSetting, function ($query, $filterSetting) {
                $setting = $this->settingPositionProductService->getSettingPositionProductById($filterSetting);
                return $query->whereHas('agent', function ($query) use ($setting) {
                    $query->whereIn('position_id', explode(',', $setting->positions));
                });
            })
            ->get();
        foreach ($activities as $activity) {
            $activity->details->each(function ($detail) use ($arrPointTypes, $activity, &$totalClosingAmount, &$totalCustomer, &$totalPoint, &$arrProductPerformance) {
                $totalClosingAmount += $detail->closing_amount;

                if (! isset($arrProductPerformance[$activity->product_id])) {
                    $arrProductPerformance[$activity->product_id] = 0;
                }
                $arrProductPerformance[$activity->product_id] += $detail->closing_amount;

                if ($detail->closing_amount > 0) {
                    $totalCustomer++;
                }

                if (isset($arrPointTypes[$detail->type_id])) {
                    $totalPoint += $arrPointTypes[$detail->type_id];
                }

            });
        }

        $arrProductPercentage = [];
        foreach ($products as $key => $value) {
            if ($filterAgent) {
                $targetProduct = $targetAgentAccounts->where('product_id', $key)->first();
            } else {
                $targetProduct = $targetUnitAccounts->where('product_id', $key)->first();
            }

            $arrProductPercentage[$key] =
            $arrProductPerformance > 0 && isset($arrProductPerformance[$key]) && $targetProduct?->total > 0 ? $arrProductPerformance[$key] / $targetProduct?->total * 100 : 0;
        }

        $isAlert = false;
        foreach ($arrProductPercentage as $key => $value) {
            if ($value < 10) {
                $isAlert = true;
                break;
            }
        }

        if (empty($filterAgent)) {
            $isAlert = false;
        }

        if (! empty($filterAgent)) {
            $percentageAccount  = $targetAgentAccount > 0 ? ($totalClosingAmount / $targetAgentAccount) * 100 : 0;
            $totalTargetAccount = $targetAgentAccount;
            $deltaAccount       = $targetAgentAccount - $totalClosingAmount;

            $percentagePoint  = $targetAgentPoint > 0 ? round(($totalPoint / $targetAgentPoint) * 100, 2) : 0;
            $totalTargetPoint = (int) $targetAgentPoint;
            $deltaPoint       = (int) $targetAgentPoint - (int) $totalPoint;
        } else {
            $percentageAccount  = $targetUnitAccount > 0 ? ($totalClosingAmount / $targetUnitAccount) * 100 : 0;
            $totalTargetAccount = $targetUnitAccount;
            $deltaAccount       = $targetUnitAccount - $totalClosingAmount;

            $percentagePoint  = $targetUnitPoint > 0 ? ($totalPoint / $targetUnitPoint) * 100 : 0;
            $totalTargetPoint = $targetUnitPoint;
            $deltaPoint       = $targetUnitPoint - $totalPoint;
        }

        if ($filterAgent) {
            $settingProduct = $this->settingPositionProductService->getSettingProductByPosition($agent?->position_id);

            $score = $this->getScore($percentageAccount, $settingProduct?->id);
        }

        $arrTopUnits = [];
        $unitData       = $this->appMasterDataService->getMasters('PMUK')->get();
        foreach ($unitData as $unit) {
            $totalTargetUnits = $this->targetUnitService->getTargetUnits()
                ->where('unit_id', $unit->id)
                ->where('period_id', $filterYear)
                ->where('type', 1)
                ->sum('total');

            $totalRealization = $this->activityDetailService->getDetails()
                ->whereHas('activity', function ($query) use ($unit) {
                    $query->whereHas('agent', function ($query) use ($unit) {
                        $query->where('unit_id', $unit->id);
                    });
                })
                ->whereHas('activity.details', function ($query) use ($startMonthDate, $endMonthDate) {
                    return $query->whereBetween('realization_date', [$startMonthDate, $endMonthDate]);
                })
                ->sum('closing_amount');

            if ($totalTargetUnits > 0 && $totalRealization > 0) {
                $arrTopUnits[$unit->id] = decimalFormat($totalRealization / $totalTargetUnits * 100);
            }
        }

        $arrDataCategories = [];

        $arrTopAgents = [];
        $agentData       = $this->agentService->getAgent(isSalesOnly: true)->get();
        foreach ($agentData as $agent) {
            $totalTargetAgents = $this->targetAgentService->getTargetAgents()
                ->where('agent_id', $agent->id)
                ->where('target_period_id', $filterYear)
                ->where('type', 1)
                ->sum('total');

            $settingProduct = $this->settingPositionProductService->getSettingProductByPosition($agent?->position_id);
            if (empty($arrDataCategories[$settingProduct?->id]["target"])) {
                $arrDataCategories[$settingProduct?->id]["target"] = 0;
            }
            $arrDataCategories[$settingProduct?->id]["target"] += $totalTargetAgents;

            $totalRealization = $this->activityDetailService->getDetails()
                ->whereHas('activity', function ($query) use ($agent) {
                    $query->where('agent_id', $agent->id);
                })
                ->whereHas('activity.details', function ($query) use ($startMonthDate, $endMonthDate) {
                    return $query->whereBetween('realization_date', [$startMonthDate, $endMonthDate]);
                })
                ->sum('closing_amount');

            if (empty($arrDataCategories[$settingProduct?->id]["realization"])) {
                $arrDataCategories[$settingProduct?->id]["realization"] = 0;
            }
            $arrDataCategories[$settingProduct?->id]["realization"] += $totalRealization;

            if ($totalTargetAgents > 0 && $totalRealization > 0) {
                $arrTopAgents[$agent->id] = decimalFormat($totalRealization / $totalTargetAgents * 100);
            }
        }

        $settingProducts = $this->settingPositionProductService->getSettingPositionProducts()
            ->where('type', 'p')
            ->get();

        $arrTopCategories = [];
        foreach ($arrDataCategories as $key => $value) {
            $percentage = $value['target'] > 0 ? decimalFormat(($value['realization'] / $value['target']) * 100) : 0;

            $arrTopCategories[$key] = $percentage;
        }

        //top 5 units sort by value
        arsort($arrTopUnits);
        $arrTopUnits = array_slice($arrTopUnits, 0, 5, true);

        //top 5 agents sort by value
        arsort($arrTopAgents);
        $arrTopAgents = array_slice($arrTopAgents, 0, 5, true);

        //top 5 categories sort by value
        arsort($arrTopCategories);
        $arrTopCategories = array_slice($arrTopCategories, 0, 5, true);

        $data = [
            'deltaAccount'         => $deltaAccount,
            'totalTargetAccount'   => $totalTargetAccount,
            'deltaPoint'           => $deltaPoint,
            'totalTargetPoint'     => $totalTargetPoint,
            'totalClosingAmount'   => $totalClosingAmount,
            'totalCustomer'        => $totalCustomer,
            'totalPoint'           => $totalPoint,
            'percentageAccount'    => round($percentageAccount),
            'percentagePoint'      => round($percentagePoint),
            'arrProductPercentage' => $arrProductPercentage,
            'score'                => $score ?? 0,
        ];

        return view('psa.home.index', [
            'unitData'            => $unitData,
            'agentData'           => $agentData,
            'units'         => $unitData->pluck('name', 'id')->toArray(),
            'agents'        => $agents,
            'settingProducts'  => $settingProducts,
            'settingPositions' => $settingPositions,
            'filterSetting'    => $filterSetting,
            'products'         => $products,
            'arrPeriods'       => $arrPeriods,
            'arrMonth'         => $arrMonth,
            'data'             => $data,
            'filterYear'       => $filterYear,
            'filterMonth'      => $filterMonth,
            'filterBranch'     => $filterBranch,
            'filterAgent'      => $filterAgent,
            'isAlert'          => $isAlert,
            'topUnits'        => $arrTopUnits,
            'topAgents'       => $arrTopAgents,
            'topCategories'   => $arrTopCategories,
        ]);
    }

    private function getScore($value, $settingProductId)
    {
        $scores = SptScore::where('setting_id', $settingProductId)->get();
        foreach ($scores as $score) {
            if (empty($score->minimum_value)) {
                if ($value >= $score->maximum_value) {
                    return $score->score;
                }
            } else if (empty($score->maximum_value)) {
                if ($value < $score->minimum_value) {
                    return $score->score;
                }
            } else {
                if ($value >= $score->minimum_value && $value < $score->maximum_value) {
                    return $score->score;
                }
            }
        }

        return 0;
    }

}
