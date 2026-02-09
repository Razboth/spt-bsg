<?php

namespace App\Services\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppMenuRequest;
use App\Repositories\Setting\AppMenuRepository;
use App\Models\Setting\AppMenu;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;

class AppMenuService extends Controller
{
    private AppMenuRepository $appMenuRepository;
    private AppModulService $appModulService;
    private array $arrPermission;

    public function __construct()
    {
        $this->appMenuRepository = new AppMenuRepository(
            new AppMenu()
        );

        $this->appModulService = new AppModulService();
        $this->arrPermission = array("view" => "View", "add" => "Create", "edit" => "Edit", "delete" => "Delete", "lvl1" => "Access Lvl 1", "lvl2" => "Access Lvl 2", "lvl3" => "Access Lvl 3");
    }

    /**
     * @throws Exception
     */
    public function getMenus($columns = [], int $parentId = 0, int $moduleId = 0, int $subModuleId = 0, $onlyActive = true): Builder
    {
        try {
            $query = $this->appMenuRepository->query();
            if (count($columns) > 0) {
                $query->select($columns);
            }

            if ($parentId > 0) {
                $query->where('parent_id', $parentId);
            }

            if ($moduleId > 0) {
                $query->where('app_modul_id', $moduleId);
            }

            if ($subModuleId > 0) {
                $query->where('app_sub_modul_id', $subModuleId);
            }

            if($onlyActive){
                $query->where('status', 't');
            }

            $query->orderBy('order');

            return $query;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function getMenuById(int $id): AppMenu
    {
        try {
            return $this->appMenuRepository->getById($id);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function getMenusForArray(int $moduleId = 0, int $subModuleId = 0, $onlyActive = true): array
    {
        try {
            $query = $this->getMenus();
            if ($moduleId > 0) {
                $query->where('app_modul_id', $moduleId);
            }

            if ($subModuleId > 0) {
                $query->where('app_sub_modul_id', $subModuleId);
            }

            if($onlyActive){
                $query->where('status', 't');
            }

            return $query->orderBy('order')->pluck('name', 'id')->toArray();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function data(Request $request, int $moduleId, int $subModuleId): Collection
    {
        return $this->getMenus(moduleId: $moduleId, subModuleId: $subModuleId, onlyActive: false)->whereNull('parent_id')->get();
    }

    /**
     * @throws Exception
     */
    public function save(AppMenuRequest $request, int $id = 0): void
    {
        $fields = [
            'parent_id' => $request->parent_id,
            'app_modul_id' => $request->app_modul_id,
            'app_sub_modul_id' => $request->app_sub_modul_id,
            'name' => $request->name,
            'target' => $request->target,
            'description' => $request->description,
            'icon' => $request->icon,
            'parameter' => $request->parameter,
            'full_screen' => $request->full_screen ?? 'f',
            'order' => $request->order,
            'status' => $request->status,
        ];

        if ($id == 0) {
            $menu = $this->appMenuRepository->create($fields);
        } else {
            $menu = $this->appMenuRepository->update($fields, $id);
        }

        Cache::forget('app_menus');

        $modul = $this->appModulService->getModuleById($request->app_modul_id);

        if ($menu->target != $request->target)
            Permission::where('name', 'like', '%' . $modul->target . "/" . $menu->target . '%')
                ->where('type', 'menu')->delete();

        foreach ($this->arrPermission as $key => $value) {
            $permission = Permission::where('name', $key . " " . $modul->target . "/" . $request->target)->first();
            if ($permission) {
                if (!isset($request->input('permission')[$key])) {
                    $permission->delete();
                }
            } else {
                if (isset($request->input('permission')[$key])) {
                    Permission::create([
                        'name' => $key . " " . $modul->target . "/" . $request->target,
                        'type_id' => $menu->id,
                        'method' => $key,
                    ]);
                }
            }
        }

        \Artisan::call('cache:clear');
    }

    public function delete(int $id): void
    {
        $this->appMenuRepository->destroy($id);
    }
}
