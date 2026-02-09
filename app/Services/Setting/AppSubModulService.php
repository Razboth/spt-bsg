<?php
namespace App\Services\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppSubModulRequest;
use App\Models\Setting\AppSubModul;
use App\Repositories\Setting\AppSubModulRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AppSubModulService extends Controller
{
    private AppSubModulRepository $appSubModulRepository;
    private AppModulService $appModulService;
    public function __construct()
    {
        $this->appSubModulRepository = new AppSubModulRepository(
            new AppSubModul()
        );
        $this->appModulService = new AppModulService();
    }

    /**
     * @throws Exception
     */
    public function getSubModules($columns = [], $moduleId = 0, $onlyActive = true): Builder
    {
        try {
            $query = $this->appSubModulRepository->query();
            if($columns) {
                $query->select($columns);
            }

            if($moduleId) {
                $query->where('app_modul_id', $moduleId);
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
    public function getSubModuleById($id)
    {
        try {
            return $this->appSubModulRepository->getById($id);
        } catch (Exception $e) {
            return throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function getSubModulesForArray($moduleId = 0): array
    {
        try {
            $query = $this->getSubModules(moduleId: $moduleId)->pluck('name', 'id')->toArray();

            return $query;
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
            $moduls = $this->appModulService->getModulesForArray();
            $filterModul = $request->get('combo_1') ?? array_key_first($moduls); //GET FILTER

            $query = $this->getSubModules(['id', 'app_modul_id', 'name', 'description', 'order', 'status'], onlyActive: false)
                ->where('app_modul_id', $filterModul);
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter))
                    $query->where('name', 'like', "%{$filter}%");
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'status', 'type' => 'status'],
                ['name' => 'app_modul_id', 'type' => 'master_relationship', 'masters' => 'appModul'],
            ], true);
        }
    }

    public function save(AppSubModulRequest $request, int $id = 0) :void
    {
        $fields = [
            'app_modul_id' => $request->app_modul_id,
            'name' => $request->name,
            'description' => $request->description,
            'order' => $request->order,
            'status' => $request->status,
        ];

        if ($id != 0) {
            $this->appSubModulRepository->update($fields, $id);
        } else {
            $this->appSubModulRepository->create($fields);
        }

        Cache::forget('app_sub_moduls');
    }

    public function delete(int $id) :void
    {
        $this->appSubModulRepository->destroy($id);

        Cache::forget('app_sub_moduls');
    }
}
