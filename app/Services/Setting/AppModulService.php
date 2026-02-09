<?php
namespace App\Services\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppModulRequest;
use App\Repositories\Setting\AppModulRepository;
use App\Models\Setting\AppModul;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;

class AppModulService extends Controller
{
    private AppModulRepository $appModulRepository;
    public function __construct()
    {
        $this->appModulRepository = new AppModulRepository(
            new AppModul()
        );
    }

    /**
     * @throws Exception
     */
    public function getModules($columns = [], $onlyActive = true): Builder
    {
        try {
            $query = $this->appModulRepository->query();
            if (count($columns) > 0) {
                $query->select($columns);
            }

            if($onlyActive){
                $query->where('status', 't');
            }

            $query->orderBy('order');

            return $query;
        } catch (Exception $e) {
            return throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function getModuleById(int $id): AppModul
    {
        try {
            return $this->appModulRepository->getById($id);
        } catch (Exception $e) {
            return throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function getModulesForArray(): array
    {
        try {
            return $this->getModules(['id', 'name'])
                ->pluck('name', 'id')
                ->toArray();
        } catch (Exception $e) {
            return throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getModules(['id', 'name', 'target', 'description', 'status', 'order'], false);
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter))
                    $query->where('name', 'like', "%{$filter}%");
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'status', 'type' => 'status'],
            ], true);
        }
    }

    public function save(AppModulRequest $request, int $id = 0): void
    {
        $fields = [
            'name' => $request->name,
            'target' => $request->target,
            'icon' => $request->icon,
            'description' => $request->description,
            'order' => $request->order,
            'status' => $request->status,
        ];

        if ($id > 0) {
            $modul = $this->appModulRepository->update($fields, $id);
        } else {
            $modul = $this->appModulRepository->create($fields);
        }

        Cache::forget('app_moduls');

        Permission::updateOrCreate([
            'name' => $request->input('target')
        ], [
            'type' => 'modul',
            'type_id' => $modul->id,
        ]);
    }

    public function delete(int $id): void
    {
        $module = $this->appModulRepository->getById($id);
        $module->subModul()->delete();
        foreach ($module->menus as $menu) {
            Permission::where('type', 'menu')->where('type_id', $menu->id)->delete();
            $menu->delete();
        }
        $module->delete();
        Cache::forget('app_moduls');

        Permission::where('name', $module->target)->where('type', 'modul')->delete();
    }
}
