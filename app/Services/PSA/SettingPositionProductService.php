<?php

namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\SettingPositionProductDetailRequest;
use App\Http\Requests\PSA\SettingPositionProductRequest;
use App\Models\PSA\SettingPositionProduct;
use App\Models\PSA\SettingPositionProductDetail;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SettingPositionProductService extends Controller
{
    private AppMasterDataService $appMasterDataService;

    public function __construct()
    {
        $this->appMasterDataService = new AppMasterDataService();
    }

    public function getSettingPositionProducts($columns = []): Builder
    {
        $query = SettingPositionProduct::query();

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getSettingPositionProductById(string $id): SettingPositionProduct
    {
        return SettingPositionProduct::find($id);
    }

    public function getSettingPositionProductForArray(): array
    {
        return $this->getSettingPositionProducts(['id', 'name'])->pluck('name', 'id')->toArray();
    }

    /**
     * @throws Exception
     */
    public function data(Request $request, $parameter): JsonResponse|null
    {
        $arrPositions = $this->appMasterDataService->getMasterForArray('PMP');
        if ($request->ajax()) {
            $query = $this->getSettingPositionProducts()->where('type', $parameter);
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('name', 'like', '%' . $filter . '%');
                }
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'status', 'type' => 'status'],
                ['name' => 'positions', 'type' => 'custom', 'value' => function ($row) use ($arrPositions) {
                    $positions = explode(',', $row->positions);
                    $position = '';
                    if (is_array($positions)) {
                        foreach ($positions as $key => $value) {
                            $position .= ($arrPositions[$value] ?? '') . ($key < count($positions) - 1 ? ', ' : '');
                        }
                    }
                    return $position;
                }],
                ['name' => 'products', 'type' => 'custom', 'value' => function ($row) {
                    if ($row->type == 'p') {
                        $route = 'psa.setting-position-products.show';
                        $parameter = 'setting_position_product';
                    } else {
                        $route = 'psa.setting-position-boosters.show';
                        $parameter = 'setting_position_booster';
                    }
                    return view('components.datatables.on-click-redirect', [
                        'route' => route($route, [
                            $parameter => $row->id,
                        ]),
                        'text' => $row->details->count()
                    ]);
                }],
            ], true);
        }

        return null;
    }

    public function save(SettingPositionProductRequest $request, string $id = ''): void
    {
        $positions = '';
        if (is_array($request->input('positions'))) $positions = implode(',', $request->input('positions'));
        $fields = [
            'name' => $request->name,
            'type' => $request->type,
            'positions' => $positions,
            'description' => $request->description,
            'status' => $request->status,
        ];

        if ($id != '') {
            $positionProduct = SettingPositionProduct::find($id);
            $positionProduct->update($fields);
        } else {
            SettingPositionProduct::create($fields);
        }
    }

    public function delete(string $id): void
    {
        $positionProduct = SettingPositionProduct::find($id);
        //delete details
        SettingPositionProductDetail::where('setting_position_product_id', $id)->delete();
        //delete position product
        $positionProduct?->delete();
    }

    public function getSettingPositionProductDetailById(string $id): SettingPositionProductDetail
    {
        return SettingPositionProductDetail::find($id);
    }

    public function saveDetail(SettingPositionProductDetailRequest $request, string $id = ''): void
    {
        $fields = [
            'setting_position_product_id' => $request->setting_position_product_id,
            'product_id' => $request->product_id,
            'weight' => $request->weight,
        ];

        if ($id != '') {
            $detail = SettingPositionProductDetail::find($id);
            $detail->update($fields);
        } else {
            SettingPositionProductDetail::create($fields);
        }
    }

    public function deleteDetail(string $id): void
    {
        $detail = SettingPositionProductDetail::find($id);
        $detail?->delete();
    }

    public function getProductsByPosition($positionId): Collection | null
    {
        $positionProduct = $this->getSettingPositionProducts()
                ->whereRaw("FIND_IN_SET(?, positions)", [$positionId])
                ->where('type', 'p')
                ->first();
            $positionProducts = $positionProduct?->details;

        return $positionProducts?->pluck('product_id');
    }

    public function getSettingProductsByPosition($positionId): Collection | null
    {
        $positionProduct = $this->getSettingPositionProducts()
                ->whereRaw("FIND_IN_SET(?, positions)", [$positionId])
                ->where('type', 'p')
                ->first();
            return $positionProduct?->details;
    }

    public function getBoostersByPosition($positionId): Collection | null
    {
        $positionProduct = $this->getSettingPositionProducts()
                ->whereRaw("FIND_IN_SET(?, positions)", [$positionId])
                ->where('type', 'b')
                ->first();
            $positionProducts = $positionProduct?->details;

        return $positionProducts?->pluck('product_id');
    }

    public function getSettingBoostersByPosition($positionId): Collection | null
    {
        $positionProduct = $this->getSettingPositionProducts()
                ->whereRaw("FIND_IN_SET(?, positions)", [$positionId])
                ->where('type', 'b')
                ->first();
            return $positionProduct?->details;
    }

    public function getSettingProductByPosition($positionId)
    {
        return $this->getSettingPositionProducts()
                ->whereRaw("FIND_IN_SET(?, positions)", [$positionId])
                ->where('type', 'p')
                ->first();
    }
}
