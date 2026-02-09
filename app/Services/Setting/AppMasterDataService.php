<?php

namespace App\Services\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppMasterDataRequest;
use App\Models\Setting\AppMasterData;
use App\Repositories\Setting\AppMasterDataRepository;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AppMasterDataService extends Controller
{

    private AppMasterDataRepository $appMasterDataRepository;
    public function __construct()
    {
        $this->appMasterDataRepository = new AppMasterDataRepository();
    }

    /**
     * @param string $code
     * @return Builder
     */
    public function getMasters(string $code): Builder
    {
        return $this->appMasterDataRepository->getMasters($code);
    }

    public function getMasterByName(string $name, $categoryCode = ''): AppMasterData | null
    {
        $query = $this->appMasterDataRepository->query()->where('name', $name);
        if($categoryCode){
            $query->where('app_master_category_code', $categoryCode);
        }
        return $query->first();
    }

    public function getMasterByCode(string $code): AppMasterData
    {
        return $this->appMasterDataRepository->query()->where('code', $code)->first();
    }

    public function getMasterById(string $id) : AppMasterData
    {
        return $this->appMasterDataRepository->getById($id);
    }

    public function getMasterForArray(string $code, string $parentId = '', string $orderField = 'order', $isForImport = false) : array
    {
        $query = $this->getMasters($code);
        if($parentId != '') $query->where('parent_id', $parentId);

        if($isForImport) return $query->orderBy($orderField)->pluck('id', DB::raw('lower(name)'))->toArray();
        return $query->orderBy($orderField)->pluck('name', 'id')->toArray();
    }

    public function getMasterByParentId(string $parentId, string $orderField = 'order', array $columns = ['id', 'name'], string $code = '') : Collection
    {
        return $this->appMasterDataRepository->getMasters($code)
            ->where('parent_id', $parentId)
            ->select($columns)
            ->orderBy($orderField)
            ->get();
    }

    public function save(AppMasterDataRequest $request, string $id = ''): void
    {
        $fields = [
            'parent_id' => $request->parent_id,
            'app_master_category_code' => $request->app_master_category_code,
            'code' => $request->code,
            'name' => $request->name,
            'parameter' => $request->parameter,
            'secret_id' => $request->secret_id,
            'description' => $request->description,
            'status' => $request->status,
            'order' => $request->order,
        ];

        if ($id == '')
            $this->appMasterDataRepository->create($fields);
        else
            $this->appMasterDataRepository->update($fields, $id);
    }

    public function getLastOrder(string $code): string
    {
        return $this->appMasterDataRepository->getMasters($code)->orderBy('order', 'desc')->value('order') + 1;
    }

    public function getLastCode(string $code): string
    {
        $lastOrder = $this->getLastOrder($code);
        return $code.str_pad($lastOrder, 3, "0", STR_PAD_LEFT);
    }
}
