<?php

namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\SettingPositionProductDetailRequest;
use App\Http\Requests\PSA\SettingPositionProductRequest;
use App\Services\PSA\ProductService;
use App\Services\PSA\SettingPositionProductService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingPositionProductController extends Controller
{
    public array $statusOption;
    private SettingPositionProductService $settingPositionProductService;
    private AppMasterDataService $appMasterDataService;
    private ProductService $productService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption = defaultStatus();
        $this->settingPositionProductService = new SettingPositionProductService();
        $this->appMasterDataService = new AppMasterDataService();
        $this->productService = new ProductService();

        \View::share('statusOption', $this->statusOption);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('psa.setting-position-products.index');
    }

    /**
     * @throws Exception
     */
    public function data(Request $request)
    {
        $parameter = getParameterMenu($this->menu_path());
        return $this->settingPositionProductService->data($request, $parameter);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $positions = $this->appMasterDataService->getMasterForArray('PMP');
        return view('psa.setting-position-products.form', [
            'positions' => $positions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SettingPositionProductRequest $request
     * @return JsonResponse
     */
    public function store(SettingPositionProductRequest $request)
    {
        $parameter = getParameterMenu($this->menu_path());
        $request->merge(['type' => $parameter]);
        return submitDataHelper(function () use ($request) {
            $this->settingPositionProductService->save($request);
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
        $setting = $this->settingPositionProductService->getSettingPositionProductById($id);
        $positions = $this->appMasterDataService->getMasterForArray('PMP');
        $settingPositions = explode(",", $setting->positions);
        return view('psa.setting-position-products.form', [
            'setting' => $setting,
            'positions' => $positions,
            'settingPositions' => $settingPositions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SettingPositionProductRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(SettingPositionProductRequest $request, string $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->settingPositionProductService->save($request, $id);
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
            $this->settingPositionProductService->delete($id);
        });
    }

    public function show($id)
    {
        $arrPositions = $this->appMasterDataService->getMasterForArray('PMP');
        $setting = $this->settingPositionProductService->getSettingPositionProductById($id);
        $positions = explode(',', $setting->positions);
        $position = '';
        if (is_array($positions)) {
            foreach ($positions as $key => $value) {
                $position .= ($arrPositions[$value] ?? '') . ($key < count($positions) - 1 ? ', ' : '');
            }
        }
        return view('psa.setting-position-products.detail', [
            'setting' => $this->settingPositionProductService->getSettingPositionProductById($id),
            'position' => $position,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function createProduct($id)
    {
        $parameter = getParameterMenu($this->menu_path());
        $setting = $this->settingPositionProductService->getSettingPositionProductById($id);
        $selectedProduct = $setting->details->pluck('product_id')->toArray();
        $products = $this->productService->getProduct()
            ->where('type', $parameter)
            ->whereNotIn('id', $selectedProduct)
            ->pluck('name', 'id')
            ->toArray();
        return view('psa.setting-position-products.form-detail', [
            'setting' => $setting,
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SettingPositionProductDetailRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function storeProduct(SettingPositionProductDetailRequest $request, $id)
    {
        return submitDataHelper(function () use ($request) {
            $this->settingPositionProductService->saveDetail($request);
        }, true, 'show', [$id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $id
     * @return Application|Factory|View
     */
    public function editProduct(string $id)
    {
        $parameter = getParameterMenu($this->menu_path());
        $detail = $this->settingPositionProductService->getSettingPositionProductDetailById($id);
        $setting = $detail->settingPositionProduct;
        $selectedProduct = $setting->details->filter(function ($item) use ($id) {
                return $item->id != $id;
            })
            ->pluck('product_id')
            ->toArray();
        $products = $this->productService->getProduct()
            ->where('type', $parameter)
            ->whereNotIn('id', $selectedProduct)
            ->pluck('name', 'id')
            ->toArray();
        return view('psa.setting-position-products.form-detail', [
            'detail' => $detail,
            'products' => $products,
            'setting' => $setting,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SettingPositionProductDetailRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function updateProduct(SettingPositionProductDetailRequest $request, string $id)
    {
        $detail = $this->settingPositionProductService->getSettingPositionProductDetailById($id);
        return submitDataHelper(function () use ($request, $id) {
            $this->settingPositionProductService->saveDetail($request, $id);
        }, true, 'show', [$detail->setting_position_product_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return RedirectResponse
     */
    public function destroyProduct(string $id)
    {
        return deleteDataHelper(function () use ($id) {
            $this->settingPositionProductService->deleteDetail($id);
        });
    }
}
