<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppModulRequest;
use App\Models\Setting\AppModul;
use App\Services\Setting\AppModulService;
use Exception;
use Illuminate\Contracts\Foundation\Application as ApplicationAlias;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse as RedirectResponseAlias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;

class AppModulController extends Controller
{
    public array $statusOption;
    private AppModulService $appModulService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption = defaultStatus();
        $this->appModulService = new AppModulService();

        \View::share('statusOption', $this->statusOption);
    }
    /**
     * Display a listing of the resource.
     *
     * @return ApplicationAlias|Factory|View
     */
    public function index()
    {
        return view('settings.app-modul.index');
    }

    /**
     * @throws Exception
     */
    public function data(Request $request)
    {
        return $this->appModulService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return ApplicationAlias|Factory|View
     */
    public function create()
    {
        return view('settings.app-modul.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AppModulRequest $request
     * @return JsonResponse
     */
    public function store(AppModulRequest $request)
    {

        return submitDataHelper(function () use ($request) {
            $this->appModulService->save($request);
        }, true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return ApplicationAlias|Factory|View
     * @throws Exception
     */
    public function edit(int $id)
    {
        $modul = $this->appModulService->getModuleById($id);

        return view('settings.app-modul.form', compact('modul'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AppModulRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(AppModulRequest $request, int $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->appModulService->save($request, $id);
        }, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponseAlias
     */
    public function destroy(int $id)
    {
        return deleteDataHelper(function () use ($id) {
            $this->appModulService->delete($id);
        });
    }
}
