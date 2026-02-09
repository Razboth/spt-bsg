<?php
namespace App\Services\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppDatasetRequest;
use App\Repositories\Setting\AppDatasetRepository;
use App\Models\Setting\AppDataset;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppDatasetService extends Controller
{
    private AppDatasetRepository $appDatasetRepository;
    public function __construct()
    {
        $this->appDatasetRepository = new AppDatasetRepository(
            new AppDataset()
        );
    }

    public function getDatasets($fields = []): Builder
    {
        $query = $this->appDatasetRepository->query();
        if ($fields) {
            $query->select($fields);
        }
        return $query;
    }

    public function getDatasetById(string $id): AppDataset
    {
        return $this->appDatasetRepository->getById($id);
    }

    public function getDatasetsForArray(): array
    {
        return $this->getDatasets()->pluck('title', 'id')->toArray();
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

            return generateDatatable($query, $queryFilter, [], true, true);
        }
    }

    public function save(AppDatasetRequest $request, string $id = ''): void
    {
        $fields = [
            'title' => $request->title,
            'code' => $request->code,
            'description' => $request->description,
        ];

        if ($id != '') {
            $this->appDatasetRepository->update($fields, $id);
        } else {
            $this->appDatasetRepository->create($fields);
        }
    }

    public function delete(string $id): void
    {
        $dataset = $this->getDatasetById($id);
        $dataset->details()->delete();
        $dataset->delete();
    }
}
