<?php

namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Imports\PSA\SalesRatioImport;
use App\Services\PSA\AgentService;
use App\Services\PSA\CustomerService;
use App\Services\PSA\ProductService;
use App\Services\PSA\SalesRatioService;
use App\Services\PSA\SettingPositionProductService;
use Auth;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SalesRatioController extends Controller
{
    private SalesRatioService $salesRatioService;
    private AgentService $agentService;
    private CustomerService $customerService;
    private ProductService $productService;
    private SettingPositionProductService $settingPositionProductService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->salesRatioService = new SalesRatioService();
        $this->agentService = new AgentService();
        $this->customerService = new CustomerService();
        $this->productService = new ProductService();
        $this->settingPositionProductService = new SettingPositionProductService();

        \View::share('arrApprove', defaultStatusApproval());
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $settingProducts = $this->settingPositionProductService->getSettingPositionProducts()
            ->where('type', 'p')
            ->pluck('name', 'id')
            ->toArray();
        $settingId = $request->get('setting_id') ?? array_key_first($settingProducts);
        if(Auth::user()->role->name != 'Manager' && Auth::user()->role->name != 'Sales') {
            $setting = $this->settingPositionProductService->getSettingPositionProductById($settingId);
        }else{
            $position = Auth::user()->model->position_id;
            $setting = $this->settingPositionProductService->getSettingProductByPosition($position);
            $settingId = $setting->id;
        }

        return view('psa.sales-ratio.index', [
            'settingProducts' => $settingProducts,
            'setting' => $setting,
            'settingId' => $settingId,
        ]);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request, $id)
    {
        return $this->salesRatioService->data($request, $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $agentId
     * @param string $customerId
     * @param string $date
     * @return Application|Factory|View
     */
    public function edit(string $agentId, string $customerId, string $date)
    {
        $agent = $this->agentService->getAgentById($agentId);
        $customer = $this->customerService->getCustomerById($customerId);
        $productSettings = $this->settingPositionProductService->getProductsByPosition($agent->position_id);
        $products = $this->productService->getProduct()
            ->whereIn('id', $productSettings->toArray())
            ->pluck('name', 'id')
            ->toArray();;
        $salesRatios = $this->salesRatioService->getSalesRatios()
            ->where('agent_id', $agentId)
            ->where('customer_id', $customerId)
            ->where('date', '<', $date)
            ->orderBy('date', 'desc')
            ->get();
        $salesRatio = $this->salesRatioService->getSalesRatios()
            ->where('agent_id', $agentId)
            ->where('customer_id', $customerId)
            ->where('date', $date)
            ->first();
        return view('psa.sales-ratio.form', [
            'products' => $products,
            'agent' => $agent,
            'customer' => $customer,
            'date' => $date,
            'salesRatios' => $salesRatios,
            'salesRatio' => $salesRatio,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param string $agentId
     * @param string $customerId
     * @param string $date
     * @return RedirectResponse
     */

    public function update(Request $request, string $agentId, string $customerId, string $date)
    {
        return submitDataHelper(function () use ($request, $agentId, $customerId, $date) {
            $this->salesRatioService->save($request, $agentId, $customerId, $date);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(string $id)
    {
        return deleteDataHelper(function () use ($id) {
            $this->salesRatioService->delete($id);
        });
    }

    public function approve($id)
    {
        $data['data'] = $this->salesRatioService->getSalesRatioById($id);
        $data['data']->approved_status = $data['data']->approved_status == 'p' ? 't' : $data['data']->approved_status;
        $data['actionRoute'] = route(str_replace('/', '.', $this->menu_path()) . '.update-approve', [$id]);
        $data['title'] = 'Approve Booster';

        return view('components.form.approve-form', $data);
    }

    public function updateApprove(Request $request, $id)
    {
        $salesRatio = $this->salesRatioService->getSalesRatioById($id);
        return submitDataHelper(function () use ($request, $salesRatio) {
            $salesRatio->update([
                'approved_date' => resetDate($request->approved_date),
                'approved_by' => Auth::user()->id,
                'approved_status' => $request->approved_status,
                'approved_note' => $request->approved_note,
            ]);
        }, true);
    }

    public function import()
    {
        return view('components.form.import-form', [
            'menu_path' => $this->menu_path(),
            'title' => 'Data Import Saldo Core',
            'url' => asset('storage/templates/format-upload-core.xlsx'),
        ]);
    }

    public function processImport(Request $request)
    {
        return importHelper(new SalesRatioImport(), $request);
    }

    public function sales($unitId)
    {
        return $this->agentService->getAgent(isSalesOnly: true)
            ->where('unit_id', $unitId)
            ->get();
    }

    public function customers($agentId)
    {
        return $this->customerService->getCustomers()
            ->where('agent_id', $agentId)
            ->get();
    }

    public function products($agentId)
    {
        $positionId = $this->agentService->getAgentById($agentId)->position_id;
        $productSettings = $this->settingPositionProductService->getProductsByPosition($positionId);
        return $this->productService->getProduct()
            ->whereIn('id', $productSettings->toArray())
            ->get();
    }
}
