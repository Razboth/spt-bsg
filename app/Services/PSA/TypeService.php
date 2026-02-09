<?php
namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\ProductRequest;
use App\Http\Requests\PSA\TypeRequest;
use App\Models\PSA\Product;
use App\Repositories\PSA\TypeRepository;
use App\Models\PSA\Type;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TypeService extends Controller
{
    private TypeRepository $typeRepository;
    public function __construct()
    {
        $this->typeRepository = new TypeRepository(
            new Type()
        );
    }

    public function getTypes($columns = []): Builder
    {
        $query = $this->typeRepository->query();

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getTypeById(string $id): Type
    {
        return $this->typeRepository->getById($id);
    }

    public function getTypeForArray(): array
    {
        return $this->getTypes(['id', 'name'])->orderBy('order')->pluck('name', 'id')->toArray();
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getTypes();
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('name', 'like', '%' . $filter . '%');
                }
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'icon', 'type' => 'photo'],
                ['name' => 'status', 'type' => 'status'],
                ['name' => 'icon', 'type' => 'custom', 'value' => function ($row) {
                    return view('psa.types.icon', [
                        'icon' => $row->icon,
                        'color' => $row->color,
                    ]);
                }]
            ], true);
        }
    }

    public function save(TypeRequest $request, string $id = ''): void
    {
        if(is_array($request->get('activity_type'))){
            $types = implode(",", $request->get('activity_type'));
        }else{
            $types = $request->get('activity_type');
        }
        $fields = [
            'name' => $request->name,
            'point' => $request->point,
            'description' => $request->description,
            'activity_type' => $types,
            'color' => $request->color,
            'icon' => $request->icon,
            'status' => $request->status,
            'order' => $request->order,
        ];

        if ($id != '') {
            $this->typeRepository->update($fields, $id);
        } else {
            $this->typeRepository->create($fields);
        }
    }

    public function delete(string $id): void
    {
        $this->typeRepository->destroy($id);
    }

    public function lastOrder()
    {
        return ($this->getTypes()->orderBy('order', 'desc')->first()->order ?? 0) + 1;
    }
}
