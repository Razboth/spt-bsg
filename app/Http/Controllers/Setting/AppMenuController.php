<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppMenuRequest;
use App\Models\Setting\AppMenu;
use App\Models\Setting\AppModul;
use App\Models\Setting\AppSubModul;
use App\Services\Setting\AppMenuService;
use App\Services\Setting\AppModulService;
use App\Services\Setting\AppSubModulService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse as RedirectResponseAlias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class AppMenuController extends Controller
{
    public array $statusOption;
    public array $moduls;
    public array $arrPermission;
    private AppMenuService $appMenuService;
    private AppModulService $appModulService;
    private AppSubModulService $appSubModulService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption = defaultStatus();
        $this->moduls = AppModul::active()
            ->orderBy('order')
            ->pluck('name', 'id')
            ->toArray();
        $this->arrPermission = array("view" => "View", "add" => "Create", "edit" => "Edit", "delete" => "Delete", "lvl1" => "Access Lvl 1", "lvl2" => "Access Lvl 2", "lvl3" => "Access Lvl 3");
        $this->appMenuService = new AppMenuService();
        $this->appModulService = new AppModulService();
        $this->appSubModulService = new AppSubModulService();

        \View::share('statusOption', $this->statusOption);
        \View::share('moduls', $this->moduls);
        \View::share('arrPermission', $this->arrPermission);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        $firstModul = $this->appModulService->getModules()
            ->where('status', 't')
            ->orderBy('order')
            ->value('id');
        $filterModule = $request->get('filterModule') ?? $firstModul;
        $submoduls = $this->appSubModulService->getSubModules()
            ->where('app_modul_id', $filterModule)
            ->where('status', 't')
            ->orderBy('order')
            ->pluck('name', 'id')
            ->toArray();
        $filterSubModule = $request->get('filterSubModule') ?? array_key_first($submoduls);
        $menus = $this->appMenuService->data($request, $filterModule, $filterSubModule);

        return view('settings.app-menu.index', [
            'menus' => $menus,
            'submoduls' => $submoduls,
            'filterModule' => $filterModule,
            'filterSubModule' => $filterSubModule,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     * @throws Exception
     */
    public function create(Request $request)
    {
        $data['filterModul'] = $request->get('filterModul') ?? $this->appModulService->getModules()
            ->where('status', 't')
            ->orderBy('order')
            ->value('id');

        $getFirstChildFromModul = $this->appSubModulService->getSubModules(moduleId: $data['filterModul'])
            ->orderBy('order')
            ->value('id');
        $filterSubModul = $request->get('filterSubModul') ?? $getFirstChildFromModul;
        $isSubModulAreChildFromSelectedModul = $this->appSubModulService->getSubModules(moduleId: $data['filterModul'])->where('id', $filterSubModul)->exists();
        $data['filterSubModul'] = $isSubModulAreChildFromSelectedModul ? $filterSubModul : $getFirstChildFromModul;
        $data['menus'] = $this->appMenuService->getMenusForArray(subModuleId: $data['filterSubModul']);
        $data['submoduls'] = $this->appSubModulService->getSubModulesForArray($data['filterModul']);

        return view('settings.app-menu.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AppMenuRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(AppMenuRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->appMenuService->save($request);
        }, true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View
     * @throws Exception
     */
    public function edit(int $id)
    {
        $data['menu'] = $this->appMenuService->getMenuById($id);
        if($data['menu']->parent_id != null){
            $data['parent_id'] = $data['menu']->parent_id;
            $data['parent'] = $data['menu']->parent;
        }
        $data['submoduls'] = $this->appSubModulService->getSubModulesForArray(moduleId: $data['menu']->app_modul_id);
        $data['menus'] = $this->appMenuService->getMenusForArray(subModuleId: $data['menu']->app_sub_modul_id);
        $modul = $this->appModulService->getModuleById($data['menu']->app_modul_id);
        $permissions = Permission::where('name', 'like', '%' . $modul->target . '/' . $data['menu']->target . '%')->pluck('name')->toArray();
        foreach ($permissions as $permission) {
            $method = explode(" ", $permission)[0];
            $data['permissions'][$method] = $method;
        }

        return view('settings.app-menu.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AppMenuRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(AppMenuRequest $request, int $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->appMenuService->save($request, $id);
        }, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponseAlias
     */
    public function destroy(int $id)
    {
        return deleteDataHelper(function () use ($id) {
            $this->appMenuService->delete($id);
        });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     * @throws Exception
     */
    public function createChild(int $id)
    {
        return view('settings.app-menu.form', [
            'parent_id' => $id,
            'parent' => $this->appMenuService->getMenuById($id),
        ]);
    }

    /**
     * @throws Exception
     */
    public function subModuls($id)
    {
        return $this->appSubModulService->getSubModules(moduleId: $id)->get();
    }

    /**
     * @throws Exception
     */
    public function menus($id)
    {
        return $this->appMenuService->getMenus(subModuleId: $id)->get();
    }
}
