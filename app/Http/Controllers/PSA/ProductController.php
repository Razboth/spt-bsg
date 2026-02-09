<?php

namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\ProductRequest;
use App\Models\PSA\Product;
use App\Services\PSA\ProductService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public array $statusOption;
    private ProductService $productService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption = defaultStatus();
        $this->productService = new ProductService();

        \View::share('statusOption', $this->statusOption);
        \View::share('typeOption', Product::PRODUCT_TYPE);
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
        return $this->productService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'lastOrder' => $this->productService->lastOrder(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductRequest $request
     * @return JsonResponse
     */
    public function store(ProductRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->productService->save($request);
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
        $product = $this->productService->getProductById($id);
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'product' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(ProductRequest $request, string $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->productService->save($request, $id);
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
            $this->productService->delete($id);
        });
    }

     public function show($id)
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.detail', [
            'product' => $this->productService->getProductById($id),
        ]);
    }


    public function detailProduct($id)
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.detail-product', [
            'product' => $this->productService->getProductById($id),
        ]);
    }

    /**
     * @throws Exception
     */
    public function dataChild(Request $request, $id)
    {
        return $this->productService->dataChild($request, $id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function createChild($id)
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form-detail', [
            'product' => $this->productService->getProductById($id),
            'lastOrder' => $this->productService->lastOrder($id),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function storeChild(ProductRequest $request, $id)
    {
        return submitDataHelper(function () use ($request, $id) {
            $this->productService->save($request, parentId: $id);
        }, true, 'show', [$id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $id
     * @return Application|Factory|View
     */
    public function editChild(string $id)
    {
        $detail = $this->productService->getProductById($id);
        return view(Str::replace('/', '.', $this->menu_path()) . '.form-detail', [
            'detail' => $detail,
            'product' => $detail->parent,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function updateChild(ProductRequest $request, string $id)
    {
        $product = $this->productService->getProductById($id);
        return submitDataHelper(function () use ($request, $id, $product) {
            $this->productService->save($request, $id, parentId: $product->parent_id);
        }, true, 'show', [$product->parent_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return RedirectResponse
     */
    public function destroyChild(string $id)
    {
        return deleteDataHelper(function () use ($id) {
            $this->productService->delete($id);
        });
    }
}
