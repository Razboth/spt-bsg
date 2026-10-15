<?php

namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Services\PSA\ActivityDetailService;
use App\Services\PSA\ActivityService;
use App\Services\PSA\ProductService;
use App\Services\PSA\SettingPositionProductService;
use App\Services\PSA\TargetAgentService;
use App\Services\PSA\TargetPeriodService;
use App\Services\PSA\TypeService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PipelineController extends Controller
{
    private TargetAgentService $targetAgentService;
    private TargetPeriodService $targetPeriodService;
    private ProductService $productService;
    private TypeService $typeService;
    private ActivityDetailService $activityDetailService;
    private SettingPositionProductService $settingPositionProductService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->targetAgentService = new TargetAgentService();
        $this->targetPeriodService = new TargetPeriodService();
        $this->productService = new ProductService();
        $this->typeService = new TypeService();
        $this->activityDetailService = new ActivityDetailService();
        $this->settingPositionProductService = new SettingPositionProductService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function salesAccount(Request $request)
    {
        $agentId = \Auth::user()->model_id;
        $periods = $this->targetPeriodService->getTargetPeriodForArray();
        $positionProducts = $this->settingPositionProductService->getProductsByPosition(\Auth::user()->employee()?->position_id);
        $positionBoosters = $this->settingPositionProductService->getBoostersByPosition(\Auth::user()->employee()?->position_id);

        $products = $this->productService->getProduct()
            ->whereIn('id', array_merge($positionProducts->toArray(), $positionBoosters->toArray()))
            ->orderBy('type', 'desc')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $filterPeriod = $request->input('filterPeriod') ?? array_key_first($periods);
        $filterProduct = $request->input('filterProduct') ?? array_key_first($products);
        $period = $this->targetPeriodService->getTargetPeriodById($filterPeriod);
        $targetAgent = $this->targetAgentService->getTargetAgents()
            ->where('agent_id', $agentId)
            ->where('target_period_id', $filterPeriod)
            ->where('product_id', $filterProduct)
            ->where('type', '1')
            ->first();
        $realizations = $this->activityDetailService->getDetails()
            ->whereHas('activity', function ($query) use ($agentId, $filterProduct, $period) {
                $query->where('agent_id', $agentId)
                    ->where('product_id', $filterProduct)
                    ->where('realization_date', '>=', $period->start_date)
                    ->where('realization_date', '<=', $period->end_date)
                    ->where('closing_amount', '>', 0);
            })
            ->get();
        $arrTotalRealization = [];
        foreach ($realizations as $realization) {
            list($year, $month, $date) = explode('-', $realization->realization_date);
            if (empty($arrTotalRealization[$month])) {
                $arrTotalRealization[$month] = 0;
            }

            $arrTotalRealization[$month] += $realization->closing_amount;
        }

        $totalRealization = $realizations->sum('closing_amount');
        return view('psa.pipelines.sales-account', [
            'targetAgent' => $targetAgent,
            'periods' => $periods,
            'filterPeriod' => $filterPeriod,
            'filterProduct' => $filterProduct,
            'products' => $products,
            'realizations' => $realizations,
            'arrTotalRealization' => $arrTotalRealization,
            'totalRealization' => $totalRealization,
        ]);
    }

    public function salesActivity(Request $request)
    {
        $agentId = \Auth::user()->model_id;
        $periods = $this->targetPeriodService->getTargetPeriodForArray();
        $products = $this->typeService->getTypes()->pluck('name', 'id')->toArray();
        $filterPeriod = $request->input('filterPeriod') ?? array_key_first($periods);
        $filterProduct = $request->input('filterProduct') ?? array_key_first($products);
        $period = $this->targetPeriodService->getTargetPeriodById($filterPeriod);
        $targetAgent = $this->targetAgentService->getTargetAgents()
            ->where('agent_id', $agentId)
            ->where('target_period_id', $filterPeriod)
            ->where('product_id', $filterProduct)
            ->where('type', 2)
            ->first();
        $realizations = $this->activityDetailService->getDetails()
            ->whereHas('activity', function ($query) use ($agentId, $filterProduct, $period) {
                $query->where('agent_id', $agentId)
                    ->where('type_id', $filterProduct)
                    ->where('realization_date', '>=', $period->start_date)
                    ->where('realization_date', '<=', $period->end_date);
            })
            ->get();

        $arrPointTypes = $this->typeService->getTypes()
            ->pluck('point', 'id')->toArray();
        $arrTotalRealization = [];
        foreach ($realizations as $realization) {
            list($year, $month, $date) = explode('-', $realization->realization_date);
            if (empty($arrTotalRealization[$month])) {
                $arrTotalRealization[$month] = 0;
            }

            $arrTotalRealization[$month] += $arrPointTypes[$realization->type_id];
        }
        return view('psa.pipelines.sales-activity', [
            'targetAgent' => $targetAgent,
            'periods' => $periods,
            'filterPeriod' => $filterPeriod,
            'products' => $products,
            'filterProduct' => $filterProduct,
            'realizations' => $realizations,
            'arrTotalRealization' => $arrTotalRealization,
        ]);
    }
}
