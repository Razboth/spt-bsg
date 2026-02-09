<?php
namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\ProductRequest;
use App\Models\PSA\Product;
use App\Repositories\PSA\ProductRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductService extends Controller
{
    private ProductRepository $productRepository;
    public function __construct()
    {
        $this->productRepository = new ProductRepository(
            new Product()
        );
    }

    public function getProduct($columns = []): Builder
    {
        $query = $this->productRepository->query();

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getProductById(string $id): Product
    {
        return $this->productRepository->getById($id);
    }

    public function getProductForArray($parentId = null, $type = null): array
    {
        return $this->getProduct(['id', 'name'])
            ->when($parentId == null, function ($query) {
                return $query->where('parent_id', null);
            })
            ->when($type != null, function ($query) use ($type) {
                return $query->where('type', $type);
            })
            ->when($parentId, function ($query, $parentId) {
                return $query->where('parent_id', $parentId);
            })
            ->pluck('name', 'id')->toArray();
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query       = $this->getProduct()->where('parent_id', null);
            $filter      = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('name', 'like', '%' . $filter . '%');
                }
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'icon', 'type' => 'photo'],
                ['name' => 'status', 'type' => 'status'],
                ['name' => 'children', 'type' => 'custom', 'value' => function ($row) {
                    return view('components.datatables.on-click-redirect', [
                        'route' => route('psa.products.show', $row->id),
                        'text'  => $row->children->count(),
                    ]);
                }],
                ['name' => 'weight', 'type' => 'custom', 'value' => function ($row) {
                    return $row->weight . " %";
                }],
            ], true, arrCustomAction: [
                [
                    'route'  => 'detail-product',
                    'ids' => ['id'],
                    'icon' => 'fa fa-magnifying-glass',
                    'isModal' => true,
                ],
            ]);
        }
    }

    public function save(ProductRequest $request, string $id = '', string $parentId = null): void
    {
        $fields = [
            'parent_id'   => $parentId,
            'name'        => $request->name,
            'description' => $request->description,
            'weight'      => $request->weight,
            'type'        => $request->type,
            'status'      => $request->status,
            'order'       => $request->order,
        ];

        if ($id != '') {
            $product = $this->productRepository->update($fields, $id);
        } else {
            $product = $this->productRepository->create($fields);
        }

        defaultUploadFile($product, $request, Product::FILE_PATH, 'product_' . Str::slug($product->name) . '_' . time(), "icon");
    }

    public function delete(string $id): void
    {
        $product = $this->productRepository->getById($id);

        //delete file
        if ($product->icon) {
            deleteFile($product->icon);
        }

        //delete child if exist
        $product->children()->delete();

        //delete product
        $this->productRepository->destroy($id);
    }

    public function lastOrder($parentId = null)
    {
        return ($this->getProduct()
                ->when($parentId, function ($query, $parentId) {
                    return $query->where('parent_id', $parentId);
                })
                ->orderBy('order', 'desc')->first()->order ?? 0) + 1;
    }

    public function dataChild(Request $request, $id): JsonResponse
    {
        if ($request->ajax()) {
            $query       = $this->getProduct()->where('parent_id', $id);
            $filter      = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('name', 'like', '%' . $filter . '%');
                }
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'icon', 'type' => 'photo'],
                ['name' => 'status', 'type' => 'status'],
            ], true, customPrefix: 'child');
        }
    }
}
