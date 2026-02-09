<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppDatasetDetailRequest;
use App\Http\Requests\Setting\AppDatasetRequest;
use App\Services\Setting\AppDatasetDetailService;
use App\Services\Setting\AppDatasetService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppDatasetController extends Controller
{
    private AppDatasetService $appDatasetService;
    private AppDatasetDetailService $appDatasetDetailService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->appDatasetService = new AppDatasetService();
        $this->appDatasetDetailService = new AppDatasetDetailService();
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
        return $this->appDatasetService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AppDatasetRequest $request
     * @return JsonResponse
     */
    public function store(AppDatasetRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->appDatasetService->save($request);
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
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'dataset' => $this->appDatasetService->getDatasetById($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AppDatasetRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(AppDatasetRequest $request, string $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->appDatasetService->save($request, $id);
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
            $this->appDatasetService->delete($id);
        });
    }

    public function show(string $id)
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.detail', [
            'dataset' => $this->appDatasetService->getDatasetById($id),
        ]);
    }

    /**
     * @throws Exception
     */
    public function dataDetail(Request $request)
    {
        return $this->appDatasetDetailService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function createDetail(string $id)
    {
        $dataset = $this->appDatasetService->getDatasetById($id);

        return view(Str::replace('/', '.', $this->menu_path()) . '.form-detail', [
            'dataset' => $dataset,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AppDatasetDetailRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function storeDetail(AppDatasetDetailRequest $request, string $id)
    {
        return submitDataHelper(function () use ($request) {
            $this->appDatasetDetailService->save($request);
        }, true, 'show', [$id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $id
     * @return Application|Factory|View
     */
    public function editDetail(string $id)
    {
        $detail = $this->appDatasetDetailService->getDatasetDetailById($id);
        return view(Str::replace('/', '.', $this->menu_path()) . '.form-detail', [
            'detail' => $detail,
            'dataset' => $detail->dataset,

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AppDatasetDetailRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function updateDetail(AppDatasetDetailRequest $request, string $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->appDatasetDetailService->save($request, $id);
        }, true, 'show', [$request->dataset_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return RedirectResponse
     */
    public function destroyDetail(string $id)
    {
        return deleteDataHelper(function () use ($id) {
            $this->appDatasetDetailService->delete($id);
        });
    }
}
