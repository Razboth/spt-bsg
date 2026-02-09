<?php

namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\TargetUnitRequest;
use App\Services\PSA\ActivityDetailService;
use App\Services\PSA\ProductService;
use App\Services\PSA\TargetPeriodService;
use App\Services\PSA\TargetUnitService;
use App\Services\PSA\TypeService;
use App\Services\Setting\AppMasterDataService;
use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalesTargetController extends Controller
{
    public array $statusOption;
    private ProductService $productService;
    private TargetPeriodService $targetPeriodService;
    private AppMasterDataService $appMasterDataService;
    private TargetUnitService $targetUnitService;
    private TypeService $typeService;
    private ActivityDetailService $activityDetailService;
    private string $type;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption = defaultStatus();
        $this->productService = new ProductService();
        $this->targetPeriodService = new TargetPeriodService();
        $this->appMasterDataService = new AppMasterDataService();
        $this->targetUnitService = new TargetUnitService();
        $this->typeService = new TypeService();
        $this->activityDetailService = new ActivityDetailService();
        $this->type = $this->menu_path() == 'psa/sales-target-account' ? '1' : '2';

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
        $units = $this->appMasterDataService->getMasterForArray('PMUK');
        $products = $this->type == '1' ? $this->productService->getProduct()
            ->orderBy('type', 'desc')
            ->orderBy('name')
            ->pluck('name', 'id')->toArray() : $this->typeService->getTypes()
            ->pluck('name', 'id')
            ->toArray();
        if(Auth::user()->role->name != 'Manager' && Auth::user()->role->name != 'Pemimpin Cabang') {
            $filterUnit = $request->input('filterUnit') ?? array_key_first($units);
        }else{
            $filterUnit = Auth::user()->model->unit_id;
        }
        $filterPeriod = $request->input('filterPeriod') ?? array_key_first($periods);
        $filterProduct = $request->input('filterProduct') ?? array_key_first($products);
        $period = $this->targetPeriodService->getTargetPeriodById($filterPeriod);
        $targetUnit = $this->targetUnitService->getTargetUnits()
            ->where('unit_id', $filterUnit)
            ->where('period_id', $filterPeriod)
            ->where('product_id', $filterProduct)
            ->where('type', $this->type)
            ->first();

        $realizations = $this->activityDetailService->getDetails()
            ->whereHas('activity', function ($query) use ($filterProduct, $filterUnit, $period) {
                $query->whereHas('agent', function ($query) use ($filterProduct, $filterUnit, $period) {
                    $query->where('unit_id', $filterUnit);
                })
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

            if($this->type == '1') {
                $arrTotalRealization[$month] += $realization->closing_amount;
            }else{
                $arrTotalRealization[$month] += $arrPointTypes[$realization->type_id];
            }
        }

        $totalRealization = $realizations->sum('closing_amount');
        return view('psa.sales-target.index', [
            'products' => $products,
            'units' => $units,
            'periods' => $periods,
            'filterUnit' => $filterUnit,
            'filterPeriod' => $filterPeriod,
            'targetUnit' => $targetUnit,
            'filterProduct' => $filterProduct,
            'arrTotalRealization' => $arrTotalRealization,
            'totalRealization' => $totalRealization,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function editBatch(string $unitId, string $periodId, string $productId)
    {
        $target = $this->targetUnitService->getTargetUnits()
            ->where('unit_id', $unitId)
            ->where('period_id', $periodId)
            ->where('product_id', $productId)
            ->where('type', $this->type)
            ->first();
        return view('psa.sales-target.form', [
            'unit' => $this->appMasterDataService->getMasterById($unitId),
            'period' => $this->targetPeriodService->getTargetPeriodById($periodId),
            'product' => $this->type == '1' ? $this->productService->getProductById($productId) : $this->typeService->getTypeById($productId),
            'target' => $target
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TargetUnitRequest $request
     * @return JsonResponse
     */
    public function store(TargetUnitRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->targetUnitService->save($request);
        }, true, 'index', ['filterUnit' => $request->get('unit_id'), 'filterPeriod' => $request->get('period_id'), 'filterProduct' => $request->get('product_id')]);
    }
}
