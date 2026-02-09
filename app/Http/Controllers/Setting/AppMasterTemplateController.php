<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppMasterTemplateRequest;
use App\Models\Setting\AppMasterTemplate;
use App\Services\Setting\AppDatasetService;
use App\Services\Setting\AppMasterDataService;
use App\Services\Setting\AppMasterTemplateService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppMasterTemplateController extends Controller
{
    private AppMasterTemplateService $appMasterTemplateService;
    private AppMasterDataService $appMasterDataService;
    private AppDatasetService $appDatasetService;
    public function __construct()
    {
        $this->middleware('auth');
        $this->appMasterTemplateService = new AppMasterTemplateService();
        $this->appMasterDataService = new AppMasterDataService();
        $this->appDatasetService = new AppDatasetService();
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
        return $this->appMasterTemplateService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'datasets' => $this->appDatasetService->getDatasetsForArray(),
            'pages' => $this->appMasterDataService->getMasterForArray('SMKT'),
            'types' => AppMasterTemplate::TEMPLATE_OPTIONS,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AppMasterTemplateRequest $request
     * @return JsonResponse
     */
    public function store(AppMasterTemplateRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->appMasterTemplateService->save($request);
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
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'template' => $this->appMasterTemplateService->getMasterTemplateById($id),
            'datasets' => $this->appDatasetService->getDatasetsForArray(),
            'pages' => $this->appMasterDataService->getMasterForArray('SMKT'),
            'types' => AppMasterTemplate::TEMPLATE_OPTIONS,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AppMasterTemplateRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(AppMasterTemplateRequest $request, string $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->appMasterTemplateService->save($request, $id);
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
            $this->appMasterTemplateService->delete($id);
        });
    }

    public function getDataset(string $id)
    {
        return $this->appDatasetService->getDatasetById($id)->details;
    }
}
