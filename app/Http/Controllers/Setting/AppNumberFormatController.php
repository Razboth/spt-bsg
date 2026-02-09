<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppNumberFormatRequest;
use App\Models\Setting\AppNumberFormat;
use App\Services\Setting\AppNumberFormatService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Str;

class AppNumberFormatController extends Controller
{
    private AppNumberFormatService $appNumberFormatService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->appNumberFormatService = new AppNumberFormatService();
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
        return $this->appNumberFormatService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'variableOptions' => AppNumberFormat::variableOptions,
            'delimiterOptions' => AppNumberFormat::delimiterOptions,
            'monthOptions' => AppNumberFormat::monthOptions,
            'yearOptions' => AppNumberFormat::yearOptions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AppNumberFormatRequest $request
     * @return JsonResponse
     */
    public function store(AppNumberFormatRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->appNumberFormatService->save($request);
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
            'format' => $this->appNumberFormatService->getNumberFormatById($id),
            'variableOptions' => AppNumberFormat::variableOptions,
            'delimiterOptions' => AppNumberFormat::delimiterOptions,
            'monthOptions' => AppNumberFormat::monthOptions,
            'yearOptions' => AppNumberFormat::yearOptions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AppNumberFormatRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(AppNumberFormatRequest $request, string $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->appNumberFormatService->save($request, $id);
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
            $this->appNumberFormatService->delete($id);
        });
    }
}
