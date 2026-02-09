<?php

namespace App\Http\Controllers\PSA;

use App\Exports\PSA\ReportIncentiveRecipientExport;
use App\Exports\PSA\ReportIndividualSPTExport;
use App\Exports\PSA\ReportIndividualSPTSingleExport;
use App\Exports\PSA\ReportLocationLogExport;
use App\Http\Controllers\Controller;
use App\Models\PSA\Agent;
use App\Models\PSA\AgentBooster;
use App\Models\PSA\Product;
use App\Models\PSA\SettingBoom;
use App\Models\PSA\SettingPositionProduct;
use App\Models\PSA\SptScore;
use App\Models\PSA\TargetAgentDetail;
use App\Models\Setting\AppMasterData;
use App\Models\Setting\LocationLog;
use App\Services\PSA\ActivityService;
use App\Services\PSA\AgentService;
use App\Services\PSA\ProductService;
use App\Services\PSA\SettingPositionProductService;
use App\Services\PSA\TargetAgentService;
use App\Services\PSA\TargetPeriodService;
use App\Services\PSA\TargetUnitService;
use App\Services\Setting\AppMasterDataService;
use DB;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    private AgentService $agentService;
    private ProductService $productService;
    private ActivityService $activityService;
    private TargetAgentService $targetAgentService;
    private TargetUnitService $targetUnitService;
    private TargetPeriodService $targetPeriodService;
    private SettingPositionProductService $positionProductService;
    private AppMasterDataService $appMasterDataService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->agentService = new AgentService();
        $this->productService = new ProductService();
        $this->activityService = new ActivityService();
        $this->targetAgentService = new TargetAgentService();
        $this->targetPeriodService = new TargetPeriodService();
        $this->positionProductService = new SettingPositionProductService();
        $this->appMasterDataService = new AppMasterDataService();
        $this->targetUnitService = new TargetUnitService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function incentiveRecipient()
    {
        $periods = $this->targetPeriodService->getTargetPeriods()->get();
        $arrPeriods = $periods->pluck('name', 'id')->toArray();
        $filterMonth = date('m');
        $filterYear = date('Y');
        $settingPositions = $this->positionProductService->getSettingPositionProducts()
            ->where('type', 'p')
            ->pluck('name', 'id')
            ->toArray();
        $units = $this->appMasterDataService->getMasterForArray('PMUK');
        return view('psa.report.report-incentive-recipient', [
            'filterMonth' => $filterMonth,
            'filterYear' => $filterYear,
            'arrPeriods' => $arrPeriods,
            'settingPositions' => $settingPositions,
            'units' => $units,
        ]);
    }

    /**
     * @throws Exception
     */
    public function dataIncentiveRecipient(Request $request)
    {
        $settingPositions = $this->positionProductService->getSettingPositionProducts()
            ->where('type', 'p')
            ->pluck('name', 'id')
            ->toArray();
        if ($request->ajax()) {
            $filterSettingPosition = $request->get('combo_2') ?? array_key_first($settingPositions);
            $filterUnit = $request->get('combo_3');
            if (\Auth::user()->role->name == 'Pemimpin Cabang') {
                $filterUnit = \Auth::user()->model->unit_id;
            }
            $settingPosition = $this->positionProductService->getSettingPositionProductById($filterSettingPosition)->positions;
            $query = $this->agentService->getAgent(isSalesOnly: true)
                ->when($filterUnit, function ($query) use ($filterUnit) {
                    $query->where('unit_id', $filterUnit);
                });
            $role = \Auth::user()->role;
            if ($role->name == 'Manager') {
//                $query->where('leader_id', \Auth::user()->model_id);
                                $query->where('unit_id', \Auth::user()->model->unit_id);

            } else {
                $query->whereIn('position_id', explode(",", $settingPosition));
            }
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('name', 'like', '%' . $filter . '%')->orWhere('employee_number', 'like', '%' . $filter . '%');
                }
            };

            $data = [
                ['name' => 'photo', 'type' => 'photo'],
                ['name' => 'unit_id', 'type' => 'master_relationship', 'masters' => 'unit'],
                ['name' => 'status_incentive', 'type' => 'custom', 'value' => function () {
                    return 'Eligible';
                }],
                ['name' => 'position_id', 'type' => 'master_relationship', 'masters' => 'position'],
            ];

            return generateDatatable($query, $queryFilter, $data, editable: false, deleteable: false);
        }

        return null;
    }

    public function exportIncentiveRecipient(Request $request): BinaryFileResponse
    {
        $filter = $request->get('filter');
        $startDate = $request->get('filter_1');
        $endDate = $request->get('filter_2');
        $periodId = $request->get('combo_1');
        $settingPositionId = $request->get('combo_2');
        $unitId = $request->get('combo_3');
        if (\Auth::user()->role->name == 'Pemimpin Cabang') {
            $unitId = \Auth::user()->model->unit_id;
        }

        if (\Auth::user()->role->name == 'Manager') {
            $agent = $this->agentService->getAgent()
                ->where('leader_id', \Auth::user()->model_id)
                ->first();
            $settingPositionId = $this->positionProductService->getSettingProductByPosition($agent->position_id)?->id;
        }

        $title = 'Penerima Insentif SPT - BSG ' . date('Y-m-d H:i:s');

        return Excel::download(new ReportIncentiveRecipientExport([
                'products' => $this->productService->getProduct()->where('type', 'p')->get(),
                'title' => $title,
                'filter' => $filter,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'periodId' => $periodId,
                'settingPositionId' => $settingPositionId,
                'unitId' => $unitId,
            ]
        ), $title . '.xlsx');
    }

    public function getDataIncentive($month, $year)
    {
        $activities = $this->activityService->getActivities()
            ->whereHas('details', function ($query) use ($month, $year) {
                $query->whereMonth('realization_date', $month)
                    ->whereYear('realization_date', $year);
            })
            ->get();
        $arrData = [];

        foreach ($activities as $activity) {
            $activity->details
                ->each(function ($detail) use (&$arrData, $activity) {
                    if (!isset($arrData['realization'][$activity->agent_id][$activity->product_id])) {
                        $arrData['realization'][$activity->agent_id][$activity->product_id] = 0;
                    }
                    $arrData['realization'][$activity->agent_id][$activity->product_id] += $detail->closing_amount;
                });
        }

        $targets = TargetAgentDetail::where('month', $month)
            ->where('year', $year)
            ->get();
        foreach ($targets as $target) {
            $arrData['target'][$target->targetAgent->agent_id][$target->targetAgent->product_id] = $target->amount;
        }

        return $arrData;
    }

    public function individualSpt()
    {
        $periods = $this->targetPeriodService->getTargetPeriods()->get();
        $arrPeriods = $periods->pluck('name', 'id')->toArray();
        $settingPositions = $this->positionProductService->getSettingPositionProducts()
            ->where('type', 'p')
            ->pluck('name', 'id')
            ->toArray();
        $units = $this->appMasterDataService->getMasterForArray('PMUK');
        return view('psa.report.report-individual-spt', [
            'arrPeriods' => $arrPeriods,
            'settingPositions' => $settingPositions,
            'units' => $units,
        ]);
    }

    /**
     * @throws Exception
     */
    public function dataIndividualSpt(Request $request)
    {
        $settingPositions = $this->positionProductService->getSettingPositionProducts()
            ->where('type', 'p')
            ->pluck('name', 'id')
            ->toArray();
        if ($request->ajax()) {
            $filterSettingPosition = $request->get('combo_2') ?? array_key_first($settingPositions);
            $settingPosition = $this->positionProductService->getSettingPositionProductById($filterSettingPosition)->positions;
            $filterUnit = $request->get('combo_3');
            if (\Auth::user()->role->name == 'Pemimpin Cabang') {
                $filterUnit = \Auth::user()->model->unit_id;
            }
            $query = $this->agentService->getAgent()
                ->when($filterUnit, function ($query) use ($filterUnit) {
                    $query->where('unit_id', $filterUnit);
                });
            $role = \Auth::user()->role;
            if ($role->name == 'Manager') {
//                $query->where('leader_id', \Auth::user()->model_id);
                                $query->where('unit_id', \Auth::user()->model->unit_id);

            } else {
                $query->whereIn('position_id', explode(",", $settingPosition));
            }
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('name', 'like', '%' . $filter . '%')->orWhere('employee_number', 'like', '%' . $filter . '%');
                }
            };

            $data = [
                ['name' => 'photo', 'type' => 'photo'],
                ['name' => 'unit_id', 'type' => 'master_relationship', 'masters' => 'unit'],
                ['name' => 'position_id', 'type' => 'master_relationship', 'masters' => 'position'],
                ['name' => 'single-export', 'type' => 'custom', 'value' => function ($row) {
                    return \view('components.views.action', [
                        'url_slot' => route('psa.report-individual-spt.export-single', [
                            'agent' => $row,
                        ]),
                        'icon_slot' => 'fa fa-file-excel',
                        'isModalSlot' => false,
                    ]);
                }],
            ];

            return generateDatatable($query, $queryFilter, $data, editable: false, deleteable: false);
        }

        return null;
    }

    public function exportIndividualSpt(Request $request): BinaryFileResponse
    {
        $filterPeriod = $request->get('combo_1');
        $filterSettingPosition = $request->get('combo_2');

        if (\Auth::user()->role->name == 'Manager') {
            $agent = $this->agentService->getAgent()
                ->where('leader_id', \Auth::user()->model_id)
                ->first();
            $filterSettingPosition = $this->positionProductService->getSettingProductByPosition($agent->position_id)?->id;
        }

        $settingPosition = $this->positionProductService->getSettingPositionProductById($filterSettingPosition)->positions;
        $positions = explode(",", $settingPosition);
        $period = $this->targetPeriodService->getTargetPeriodById($filterPeriod);

        $agents = $this->agentService->getAgent()
            ->with(['unit', 'position', 'leader'])
            ->whereIn('position_id', $positions)
            ->when(\Auth::user()->role->name == 'Manager', function ($query) {
                $query->where('leader_id', \Auth::user()->model_id);
            })
            ->when(\Auth::user()->role->name == 'Pemimpin Cabang', function ($query) {
                $query->where('unit_id', \Auth::user()->model->unit_id);
            })
            ->get();

        $title = 'Penerima Insentif SPT - BSG Bulan Desember 2025';

        return Excel::download(new ReportIndividualSPTExport([
                'agents' => $agents,
                'period' => $period,
            ]
        ), $title . '.xlsx');
    }

    public function exportSingleIndividualSpt(Agent $agent): BinaryFileResponse
    {
        $period = $this->targetPeriodService->getTargetPeriods()->latest()->first();
        $agentId = $agent->id;

        if ($agent->rank_id != '7a0094e0-c97e-475f-a75e-5eedd4bb9cdd') {
            $targets = $this->targetUnitService->getTargetUnits()
                ->with('details')
                ->where('period_id', $period->id)
                ->where('unit_id', $agent->unit_id)
                ->get();

            $activities = $this->activityService->getActivities()
                ->with(['agent', 'product', 'details'])
                ->where('date', '>=', $period->start_date)
                ->where('date', '<=', $period->end_date)
                ->whereHas('agent', function ($query) use ($agent) {
                    $query->where('unit_id', $agent->unit_id);
                })
                ->get();
        } else {
            $targets = $this->targetAgentService->getTargetAgents()
                ->with('details')
                ->where('target_period_id', $period->id)
                ->where('agent_id', $agentId)
                ->get();

            $activities = $this->activityService->getActivities()
                ->with(['agent', 'product', 'details'])
                ->where('date', '>=', $period->start_date)
                ->where('date', '<=', $period->end_date)
                ->where('agent_id', $agent->id)
                ->get();
        }

        $scores = SptScore::get();
        $agentBoosters = AgentBooster::where('agent_id', $agentId)->get();
        $booms = SettingBoom::where('agent_id', $agentId)->get();
        $products = Product::get();
        $settingProducts = SettingPositionProduct::with('details')->get();

        $title = 'Penerima Insentif SPT - BSG Bulan Desember 2025';

        return Excel::download(new ReportIndividualSPTSingleExport([
                'products' => $products,
                'targets' => $targets,
                'activities' => $activities,
                'agent' => $agent,
                'scores' => $scores,
                'agentBoosters' => $agentBoosters,
                'booms' => $booms,
                'settingProducts' => $settingProducts,
            ]
        ), $title . '.xlsx');
    }

    public function locationLogs()
    {
        return view('psa.report.report-location-logs');
    }

    /**
     * @throws Exception
     */
    public function dataLocationLogs(Request $request)
    {
        if ($request->ajax()) {
            $query = LocationLog::join('app_users', 'app_location_logs.user_id', '=', 'app_users.id')
                ->join('agents', 'app_users.model_id', '=', 'agents.id')
                ->select('app_location_logs.*', 'agents.name as name');
            if (\Auth::user()->role->name == 'Manager') {
                $query->where('agents.leader_id', \Auth::user()->model_id);
            }
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->whereHas('user', function ($query) use ($filter) {
                        $query->where('name', 'like', '%' . $filter . '%')
                            ->orWhere('employee_number', 'like', '%' . $filter . '%');
                    });
                }
            };

            $data = [
                ['name' => 'name', 'type' => 'custom', 'value' => function ($row) {
                    return $row->user->model->name;
                }],
                ['name' => 'unit_id', 'type' => 'custom', 'value' => function ($row) {
                    return $row->user->model->unit->name;
                }],
                ['name' => 'position_id', 'type' => 'custom', 'value' => function ($row) {
                    return $row->user->model->position->name;
                }],
            ];

            return generateDatatable($query, $queryFilter, $data, editable: false, deleteable: false);
        }

        return null;
    }

    public function exportLocationLogs(): BinaryFileResponse
    {
        $title = 'Log Lokasi';

        $logs = LocationLog::join('app_users', 'app_location_logs.user_id', '=', 'app_users.id')
            ->join('agents', 'app_users.model_id', '=', 'agents.id')
            ->select('app_location_logs.*', 'agents.name as name')
            ->get();

        return Excel::download(new ReportLocationLogExport([
                'logs' => $logs,
            ]
        ), $title . '.xlsx');
    }

    public function maps(Request $request)
    {
        $date = $request->get('filterDate') ?? date('d/m/Y');
        $branch = $request->get('filterBranch') ?? null;
        $locationLogs = LocationLog::select(DB::raw('user_id, longitude, latitude, address, COUNT(*) as total'))
            ->whereHas('user.model', function ($query) use ($branch) {
                if ($branch) {
                    $query->where('unit_id', $branch);
                }
            })
            ->whereDate('created_at', resetDate($date))
            ->groupBy('user_id')
            ->get();

        $units = AppMasterData::whereAppMasterCategoryCode('PMUK')->pluck('name', 'id')->toArray();
        return view('psa.report.maps-activity-sales', [
            'logs' => $locationLogs,
            'filterDate' => resetDate($date),
            'filterBranch' => $branch,
            'units' => $units,
        ]);
    }
}
