<?php
namespace App\Services\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppDatasetDetailRequest;
use App\Http\Requests\Setting\AppDatasetRequest;
use App\Repositories\Setting\AppDatasetDetailRepository;
use App\Models\Setting\AppDatasetDetail;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppDatasetDetailService extends Controller
{
    private AppDatasetDetailRepository $appDatasetDetailRepository;
    public function __construct()
    {
        $this->appDatasetDetailRepository = new AppDatasetDetailRepository(
            new AppDatasetDetail()
        );
    }

    public function getDatasets($fields = []): Builder
    {
        $query = $this->appDatasetDetailRepository->query();
        if ($fields) {
            $query->select($fields);
        }
        return $query;
    }

    public function getDatasetDetailById(string $id): AppDatasetDetail
    {
        return $this->appDatasetDetailRepository->getById($id);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getDatasets();
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('title', 'like', '%' . $filter . '%')->orWhere('code', 'like', '%' . $filter . '%');
                }
            };

            return generateDatatable($query, $queryFilter, [], true, false, 'detail');
        }
    }

    public function save(AppDatasetDetailRequest $request, string $id = ''): void
    {
        $fields = [
            'dataset_id' => $request->dataset_id,
            'title' => $request->title,
            'code' => $request->code,
            'example' => $request->example,
            'description' => $request->description,
        ];

        if ($id != '') {
            $this->appDatasetDetailRepository->update($fields, $id);
        } else {
            $this->appDatasetDetailRepository->create($fields);
        }
    }

    public function delete(string $id): void
    {
        $this->appDatasetDetailRepository->destroy($id);
    }
}
