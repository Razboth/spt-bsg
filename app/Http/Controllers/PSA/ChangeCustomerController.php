<?php

namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\AgentBoosterRequest;
use App\Http\Requests\PSA\ChangeCustomerRequest;
use App\Imports\PSA\BoosterImport;
use App\Models\PSA\AgentBooster;
use App\Services\PSA\AgentBoosterService;
use App\Services\PSA\AgentService;
use App\Services\PSA\ChangeCustomerService;
use App\Services\PSA\CustomerService;
use App\Services\PSA\ProductService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChangeCustomerController extends Controller
{
    private AgentService $agentService;
    private CustomerService $customerService;
    private ChangeCustomerService $changeCustomerService;
    private AppMasterDataService $appMasterDataService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->agentService = new AgentService();
        $this->customerService = new CustomerService();
        $this->changeCustomerService = new ChangeCustomerService();
        $this->appMasterDataService = new AppMasterDataService();

        \View::share('arrApprove', defaultStatusApproval());
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('psa.change-customers.index');
    }

    /**
     * @throws Exception
     */
    public function data(Request $request)
    {
        return $this->changeCustomerService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $agents = [];
        $customers = [];
        $units = $this->appMasterDataService->getMasterForArray('PMUK');
        return view('psa.change-customers.form', [
            'agents' => $agents,
            'customers' => $customers,
            'units' => $units,
            'lastNumber' => $this->changeCustomerService->getLastNumber(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ChangeCustomerRequest $request
     * @return JsonResponse
     */
    public function store(ChangeCustomerRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->changeCustomerService->save($request);
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
        $agents = $this->agentService->getAgentForArray();
        $customers = $this->customerService->getCustomerForArray();
        $units = $this->appMasterDataService->getMasterForArray('PMUK');
        $changeCustomer = $this->changeCustomerService->getChangeCustomerById($id);
        return view('psa.change-customers.form', [
            'agents' => $agents,
            'customers' => $customers,
            'units' => $units,
            'changeCustomer' => $changeCustomer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ChangeCustomerRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(ChangeCustomerRequest $request, string $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->changeCustomerService->save($request, $id);
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
            $this->changeCustomerService->delete($id);
        });
    }

    public function approve($id)
    {
        $data['data'] = $this->changeCustomerService->getChangeCustomerById($id);
        $data['data']->approved_status = $data['data']->approved_status == 'p' ? 't' : $data['data']->approved_status;
        $data['actionRoute'] = route(str_replace('/', '.', $this->menu_path()) . '.update-approve', [$id]);
        $data['title'] = 'Approve Booster';

        return view('components.form.approve-form', $data);
    }

    public function updateApprove(Request $request, $id)
    {
        $changeCustomer = $this->changeCustomerService->getChangeCustomerById($id);
        return submitDataHelper(function () use ($request, $changeCustomer) {
            $changeCustomer->update([
                'approved_date' => resetDate($request->approved_date),
                'approved_by' => \Auth::user()->id,
                'approved_status' => $request->approved_status,
                'approved_note' => $request->approved_note,
            ]);

            $customer = $changeCustomer->customer;
            $customer->update([
                'agent_id' => $changeCustomer->to_agent_id,
            ]);
        }, true);
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
}
