<?php

namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\ActivityRequest;
use App\Http\Requests\PSA\AgentRequest;
use App\Models\Master\ProgramHasInstructor;
use App\Services\PSA\ActivityDetailService;
use App\Services\PSA\ActivityService;
use App\Services\PSA\AgentService;
use App\Services\PSA\CustomerService;
use App\Services\PSA\ProductService;
use App\Services\PSA\TypeService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AgentMappingController extends Controller
{
    private AgentService $agentService;
    private AppMasterDataService $appMasterDataService;
    private CustomerService $customerService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->agentService = new AgentService();
        $this->appMasterDataService = new AppMasterDataService();
        $this->customerService = new CustomerService();
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
        return $this->agentService->dataMapping($request);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param string $id
     * @return Application|Factory|View
     */
    public function edit(string $id)
    {
        $agent = $this->agentService->getAgentById($id);
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'agent' => $agent,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AgentRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(AgentRequest $request, string $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->agentService->save($request, $id);
        });
    }

    public function addCustomer(string $id)
    {
        $agent = $this->agentService->getAgentById($id);
        return view(Str::replace('/', '.', $this->menu_path()) . '.customer-form', [
            'agent' => $agent,
            'types' => $this->appMasterDataService->getMasterForArray('PMKC')
        ]);
    }

    /**
     * @throws Exception
     */
    public function dataCustomer(Request $request, $id)
    {
        return $this->customerService->dataMapping($request, $id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function storeCustomer(Request $request, string $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->agentService->saveCustomer($request, $id);
        }, true, 'edit', [$id]);
    }

    public function destroyCustomer(string $id)
    {
        return deleteDataHelper(function () use ($id) {
            $this->customerService->getCustomerById($id)->update([
                'agent_id' => null
            ]);
        });
    }

//    /**
//     * Remove the specified resource from storage.
//     *
//     * @param string $id
//     * @return RedirectResponse
//     */
//    public function destroy(string $id)
//    {
//        return deleteDataHelper(function () use ($id) {
//            $this->activityService->delete($id);
//        });
//    }
}
