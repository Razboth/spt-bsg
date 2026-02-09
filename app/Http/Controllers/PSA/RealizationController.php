<?php

namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\PlanRequest;
use App\Http\Requests\PSA\RealizationRequest;
use App\Services\PSA\AgentService;
use App\Services\PSA\CustomerService;
use App\Services\PSA\PlanService;
use App\Services\PSA\ProductService;
use App\Services\PSA\RealizationService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RealizationController extends Controller
{
    private RealizationService $realizationService;
    private PlanService $planService;
    private AgentService $agentService;
    private AppMasterDataService $appMasterDataService;
    private CustomerService $customerService;
    private ProductService $productService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->realizationService = new RealizationService();
        $this->planService = new PlanService();
        $this->agentService = new AgentService();
        $this->appMasterDataService = new AppMasterDataService();
        $this->customerService = new CustomerService();
        $this->productService = new ProductService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.index');
    }

    /**
     * @throws Exception
     */
    public function data(Request $request)
    {
        return $this->planService->dataRealizations($request);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $id
     * @return Application|Factory|View
     */
    public function edit(string $id)
    {
        $plan = $this->planService->getPlanById($id);
        $realization = $plan->realization;
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'plan' => $plan,
            'realization' => $realization,
            'types' => $this->appMasterDataService->getMasterForArray('PMTR'),
            'agents' => $this->agentService->getAgentForArray(),
            'customers' => $this->customerService->getCustomerForArray(),
            'products' => $this->productService->getProductForArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param RealizationRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(RealizationRequest $request, string $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->realizationService->save($request, $id);
        });
    }
}
