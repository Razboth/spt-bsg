<?php

namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\AgentBoosterRequest;
use App\Imports\PSA\BoosterImport;
use App\Models\PSA\AgentBooster;
use App\Services\PSA\AgentBoosterService;
use App\Services\PSA\AgentService;
use App\Services\PSA\CustomerService;
use App\Services\PSA\ProductService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AgentBoosterController extends Controller
{
    private AgentBoosterService $agentBoosterService;
    private ProductService $productService;
    private AgentService $agentService;
    private CustomerService $customerService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->agentBoosterService = new AgentBoosterService();
        $this->productService = new ProductService();
        $this->agentService = new AgentService();
        $this->customerService = new CustomerService();

        \View::share('arrApprove', defaultStatusApproval());
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('psa.agent-boosters.index');
    }

    /**
     * @throws Exception
     */
    public function data(Request $request)
    {
        return $this->agentBoosterService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $products = $this->productService->getProductForArray(type: 'b');
        $agents = $this->agentService->getAgentForArray();
        $customers = $this->customerService->getCustomerForArray();
        return view('psa.agent-boosters.form', [
            'products' => $products,
            'agents' => $agents,
            'customers' => $customers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AgentBoosterRequest $request
     * @return JsonResponse
     */
    public function store(AgentBoosterRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->agentBoosterService->save($request);
        }, true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $id
     * @return Application|Factory|View
     */
    public function edit(string $id)
    {
        $products = $this->productService->getProductForArray(type: 'b');
        $agents = $this->agentService->getAgentForArray();
        $agentBooster = $this->agentBoosterService->getAgentBoosterById($id);
        $customers = $this->customerService->getCustomerForArray();
        return view('psa.agent-boosters.form', [
            'products' => $products,
            'agents' => $agents,
            'customers' => $customers,
            'agentBooster' => $agentBooster,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AgentBoosterRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(AgentBoosterRequest $request, string $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->agentBoosterService->save($request, $id);
        }, true);
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
            $this->agentBoosterService->delete($id);
        });
    }

    public function import()
    {
        return view('components.form.import-form', [
            'menu_path' => $this->menu_path(),
            'title' => 'Data Import Booster',
            'url' => asset('storage/templates/format-upload-booster.xlsx'),
        ]);
    }

    public function processImport(Request $request)
    {
        return importHelper(new BoosterImport(), $request);
    }

    public function approve($id)
    {
        $data['data'] = AgentBooster::find($id);
        $data['data']->approved_status = $data['data']->approved_status == 'p' ? 't' : $data['data']->approved_status;
        $data['actionRoute'] = route(str_replace('/', '.', $this->menu_path()) . '.update-approve', [$id]);
        $data['title'] = 'Approve Booster';

        return view('components.form.approve-form', $data);
    }

    public function updateApprove(Request $request, $id)
    {
        $booster = AgentBooster::find($id);
        return submitDataHelper(function () use ($request, $booster) {
            $booster->update([
                'approved_date' => $request->approved_date,
                'approved_by' => \Auth::user()->id,
                'approved_status' => $request->approved_status,
                'approved_note' => $request->approved_note,
            ]);
        }, true);
    }
}
