<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppSubModulRequest;
use App\Models\Setting\AppModul;
use App\Models\Setting\AppSubModul;
use App\Services\Setting\AppSubModulService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

class AppSubModulController extends Controller
{
    public array $statusOption;
    public array $moduls;
    private AppSubModulService $appSubModulService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption = defaultStatus();
        $this->moduls = AppModul::active()
            ->orderBy('order')
            ->pluck('name', 'id')
            ->toArray();
        $this->appSubModulService = new AppSubModulService();

        \View::share('statusOption', $this->statusOption);
        \View::share('moduls', $this->moduls);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('settings.app-sub-modul.index');
    }

    /**
     * @throws Exception
     */
    public function data(Request $request)
    {
        return $this->appSubModulService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(Request $request)
    {
        $filterModul = $request->get('filterModul');

        return view('settings.app-sub-modul.form', compact('filterModul'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AppSubModulRequest $request
     * @return JsonResponse
     */
    public function store(AppSubModulRequest $request)
    {
        AppSubModul::create([
            'app_modul_id' => $request->input('app_modul_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'order' => $request->input('order'),
        ]);

        Cache::forget('app_sub_moduls');

        return response()->json([
            'success'=>'Sub Modul berhasil disimpan',
            'url'=> route('settings.app-sub-moduls.index')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $submodul = AppSubModul::findOrFail($id);

        return view('settings.app-sub-modul.form', compact('submodul'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AppSubModulRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(AppSubModulRequest $request, int $id)
    {
        $submodul = AppSubModul::findOrFail($id);
        $submodul->update([
            'app_modul_id' => $request->input('app_modul_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'order' => $request->input('order'),
        ]);

        Cache::forget('app_sub_moduls');

        return response()->json([
            'success'=>'Sub Modul berhasil diupdate',
            'url'=> route('settings.app-sub-moduls.index')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(int $id)
    {
        $submodul = AppSubModul::findOrFail($id);
        $submodul->delete();

        Cache::forget('app_sub_moduls');

        return response()->json([
            'success'=>'Sub Modul berhasil dihapus',
            'url'=> route('settings.app-sub-moduls.index')
        ]);
    }
}
