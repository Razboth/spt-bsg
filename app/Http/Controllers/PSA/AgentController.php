<?php

namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\AgentRequest;
use App\Imports\PSA\AgentImport;
use App\Models\PSA\Agent;
use App\Services\PSA\AgentService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AgentController extends Controller
{
    public array $genderOption;
    private AgentService $agentService;
    private AppMasterDataService $appMasterDataService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->genderOption = defaultGender();
        $this->agentService = new AgentService();
        $this->appMasterDataService = new AppMasterDataService();

        \View::share('genderOption', $this->genderOption);
        \View::share('activityTypeOptions', Agent::ACTIVITY_TYPES);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.index', [
            'ranks' => $this->appMasterDataService->getMasterForArray('PMPT'),
            'units' => $this->appMasterDataService->getMasterForArray('PMUK'),
        ]);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request)
    {
        return $this->agentService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [

            'ranks' => $this->appMasterDataService->getMasterForArray('PMPT'),
            'positions' => $this->appMasterDataService->getMasterForArray('PMP'),
            'statuses' => $this->appMasterDataService->getMasterForArray('PMSP'),
            'types' => $this->appMasterDataService->getMasterForArray('PMTP'),
            'units' => $this->appMasterDataService->getMasterForArray('PMUK'),
            'leaders' => $this->agentService->getAgentForArray(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AgentRequest $request
     * @return JsonResponse
     */
    public function store(AgentRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->agentService->save($request);
        });
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
            'ranks' => $this->appMasterDataService->getMasterForArray('PMPT'),
            'positions' => $this->appMasterDataService->getMasterForArray('PMP'),
            'statuses' => $this->appMasterDataService->getMasterForArray('PMSP'),
            'types' => $this->appMasterDataService->getMasterForArray('PMTP'),
            'units' => $this->appMasterDataService->getMasterForArray('PMUK'),
            'leaders' => $this->agentService->getAgentForArray(),
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

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(string $id)
    {
        return deleteDataHelper(function () use ($id) {
            $this->agentService->delete($id);
        });
    }

    public function import()
    {
        return view('components.form.import-form', [
            'menu_path' => $this->menu_path(),
            'title' => 'Data Import Pegawai',
            'url' => asset('storage/templates/format-upload-agent.xlsx'),
        ]);
    }

    public function processImport(Request $request)
    {
        return importHelper(new AgentImport(), $request);
    }
}
