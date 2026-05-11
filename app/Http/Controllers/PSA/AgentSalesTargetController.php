<?php
namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\TargetAgentRequest;
use App\Imports\PSA\SalesTargetAccountImport;
use App\Imports\PSA\SalesTargetActivityImport;
use App\Services\PSA\ActivityDetailService;
use App\Services\PSA\AgentService;
use App\Services\PSA\ProductService;
use App\Services\PSA\SettingPositionProductService;
use App\Services\PSA\TargetAgentService;
use App\Services\PSA\TargetPeriodService;
use App\Services\PSA\TypeService;
use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgentSalesTargetController extends Controller
{
    public array $statusOption;
    private ProductService $productService;
    private TargetPeriodService $targetPeriodService;
    private TargetAgentService $targetAgentService;
    private AgentService $agentService;
    private TypeService $typeService;
    private ActivityDetailService $activityDetailService;
    private SettingPositionProductService $settingPositionProductService;
    private string $type;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption                  = defaultStatus();
        $this->productService                = new ProductService();
        $this->agentService                  = new AgentService();
        $this->targetPeriodService           = new TargetPeriodService();
        $this->targetAgentService            = new TargetAgentService();
        $this->typeService                   = new TypeService();
        $this->activityDetailService         = new ActivityDetailService();
        $this->settingPositionProductService = new SettingPositionProductService();
        $this->type                          = $this->menu_path() == 'psa/agent-sales-target-account' ? '1' : '2';

        \View::share('statusOption', $this->statusOption);
        \View::share('type', $this->type);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $periods = $this->targetPeriodService->getTargetPeriodForArray();

        $agents = $this->agentService->getAgent(isSalesOnly: true)
            ->when(Auth::user()->role->name == 'Manager' || Auth::user()->role->name == 'Pemimpin Cabang', function ($query) {
                $query->where('unit_id', \Auth::user()->model->unit_id);
            })
            ->pluck('name', 'id')
            ->toArray();

        $filterAgent  = $request->input('filterAgent') ?? array_key_first($agents);
        $filterPeriod = $request->input('filterPeriod') ?? array_key_first($periods);

        $products = $this->type == '1' ? $this->productService->getProduct()
            ->when($filterAgent, function ($query) use ($filterAgent) {
                $agent            = $this->agentService->getAgentById($filterAgent);
                $positionProducts = $this->settingPositionProductService->getProductsByPosition($agent->position_id);
                $positionBoosters = $this->settingPositionProductService->getBoostersByPosition($agent->position_id);
                $query->whereIn('id', array_merge($positionProducts->toArray(), $positionBoosters->toArray()));
            })
            ->orderBy('type', 'desc')
            ->orderBy('name')
            ->pluck('name', 'id')->toArray() : $this->typeService->getTypes()->pluck('name', 'id')->toArray();

        $filterProduct = $request->input('filterProduct');
        if (! array_key_exists($filterProduct, $products)) {
            $filterProduct = array_key_first($products);
        }
        $period      = $this->targetPeriodService->getTargetPeriodById($filterPeriod);
        $targetAgent = $this->targetAgentService->getTargetAgents()
            ->where('agent_id', $filterAgent)
            ->where('target_period_id', $filterPeriod)
            ->where('product_id', $filterProduct)
            ->where('type', $this->type)
            ->first();

        $realizations = $this->activityDetailService->getDetails()
            ->whereHas('activity', function ($query) use ($filterAgent, $filterProduct, $period) {
                $query->where('agent_id', $filterAgent)
                    ->when($this->type == '1', function ($query) use ($filterProduct) {
                        $query->where('product_id', $filterProduct)
                            ->where('closing_amount', '>', 0);
                    })
                    ->when($this->type == '2', function ($query) use ($filterProduct) {
                        $query->where('type_id', $filterProduct);
                    })
                    ->where('realization_date', '>=', $period->start_date)
                    ->where('realization_date', '<=', $period->end_date);
            })
            ->get();

        $arrPointTypes = $this->typeService->getTypes()->pluck('point', 'id')->toArray();

        $arrTotalRealization = [];
        foreach ($realizations as $realization) {
            list($year, $month, $date) = explode('-', $realization->realization_date);
            if (empty($arrTotalRealization[$month])) {
                $arrTotalRealization[$month] = 0;
            }

            if ($this->type == '1') {
                $arrTotalRealization[$month] += $realization->closing_amount;
            } else {
                $arrTotalRealization[$month] += $arrPointTypes[$realization->type_id];
            }
        }

        $totalRealization = $realizations->sum('closing_amount');
        return view('psa.agent-sales-target.index', [
            'products'            => $products,
            'agents'              => $agents,
            'periods'             => $periods,
            'filterAgent'         => $filterAgent,
            'filterPeriod'        => $filterPeriod,
            'filterProduct'       => $filterProduct,
            'targetAgent'         => $targetAgent,
            'arrTotalRealization' => $arrTotalRealization,
            'totalRealization'    => $totalRealization,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function editBatch(string $unitId, string $periodId, string $productId)
    {
        $target = $this->targetAgentService->getTargetAgents()
            ->where('agent_id', $unitId)
            ->where('target_period_id', $periodId)
            ->where('product_id', $productId)
            ->where('type', $this->type)
            ->first();
        return view('psa.agent-sales-target.form', [
            'agent'   => $this->agentService->getAgentById($unitId),
            'period'  => $this->targetPeriodService->getTargetPeriodById($periodId),
            'product' => $this->type == '1' ? $this->productService->getProductById($productId) : $this->typeService->getTypeById($productId),
            'target'  => $target,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TargetAgentRequest $request
     * @return JsonResponse
     */
    public function store(TargetAgentRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->targetAgentService->save($request);
        }, true);
    }

    public function import()
    {
        $isActivity = $this->type == '2';
        return view('components.form.import-form', [
            'menu_path' => $this->menu_path(),
            'title'     => $isActivity ? 'Import Target Activity' : 'Import Target Account',
            'url'       => $isActivity
                ? asset('templates/format-upload-target-activity.xlsx')
                : asset('templates/format-upload-target.xlsx'),
        ]);
    }

    public function processImport(Request $request)
    {
        $import = $this->type == '2' ? new SalesTargetActivityImport() : new SalesTargetAccountImport();
        return importHelper($import, $request);
    }
}
